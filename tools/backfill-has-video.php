<?php
/**
 * Temporary one-shot tool to backfill the hasVideo ACF field.
 *
 * Upload this file with the plugin, run it once from the browser while logged in
 * as an administrator, then remove it from the server.
 */

declare(strict_types=1);

$allowedIps = [
    '127.0.0.1',
    '::1',
    '181.80.42.1',
];

$startDate = '2025-01-01';
$optionName = 'wpape_has_video_backfill_completed';
$nonceAction = 'wpape_backfill_has_video';

function wpape_backfill_find_wp_load(string $startDir): string
{
    $dir = $startDir;

    for ($i = 0; $i < 8; $i++) {
        $candidate = $dir . DIRECTORY_SEPARATOR . 'wp-load.php';

        if (is_file($candidate)) {
            return $candidate;
        }

        $parent = dirname($dir);

        if ($parent === $dir) {
            break;
        }

        $dir = $parent;
    }

    return '';
}

function wpape_backfill_client_ip(): string
{
    foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'] as $key) {
        if (empty($_SERVER[$key])) {
            continue;
        }

        $value = (string) $_SERVER[$key];
        $ip = trim(explode(',', $value)[0]);

        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return '';
}

function wpape_backfill_content_has_youtube(string $content): bool
{
    return preg_match(
        '~(?:https?:)?(?://)?(?:www\.)?(?:youtube\.com/(?:watch\?[^"\']*v=|embed/|shorts/)|youtu\.be/|youtube-nocookie\.com/embed/)~i',
        $content
    ) === 1;
}

function wpape_backfill_set_has_video(int $postId): void
{
    if (function_exists('update_field')) {
        update_field('field_wpape_has_video', ['1'], $postId);
        return;
    }

    update_post_meta($postId, 'hasVideo', ['1']);
}

function wpape_backfill_has_video_meta(int $postId): bool
{
    $value = get_post_meta($postId, 'hasVideo', true);

    if (is_array($value)) {
        return in_array('1', $value, true) || in_array(1, $value, true);
    }

    return $value === '1' || $value === 1 || $value === true;
}

$wpLoad = wpape_backfill_find_wp_load(dirname(__DIR__));

if ($wpLoad === '') {
    http_response_code(500);
    echo 'No se pudo encontrar wp-load.php.';
    exit;
}

require_once $wpLoad;

$endDate = current_time('Y-m-d');

$clientIp = wpape_backfill_client_ip();

if (!empty($allowedIps) && !in_array($clientIp, $allowedIps, true)) {
    wp_die('IP no autorizada para ejecutar esta herramienta.');
}

if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('Necesitas iniciar sesion como administrador para ejecutar esta herramienta.');
}

$completedAt = get_option($optionName);

if ($completedAt) {
    wp_die(
        'Esta herramienta ya fue ejecutada el ' . esc_html((string) $completedAt) . '. Elimina este archivo del servidor.'
    );
}

$didRun = isset($_POST['wpape_run_backfill']);
$isDryRun = isset($_POST['wpape_dry_run']);
$summary = null;
$matchedPosts = [];

if ($didRun) {
    check_admin_referer($nonceAction);

    @set_time_limit(0);

    $summary = [
        'checked' => 0,
        'matched' => 0,
        'updated' => 0,
        'already_marked' => 0,
    ];

    $paged = 1;

    do {
        $query = new WP_Query([
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 200,
            'paged' => $paged,
            'fields' => 'ids',
            'orderby' => 'ID',
            'order' => 'ASC',
            'date_query' => [
                [
                    'after' => $startDate . ' 00:00:00',
                    'before' => $endDate . ' 23:59:59',
                    'inclusive' => true,
                ],
            ],
            'no_found_rows' => false,
        ]);

        foreach ($query->posts as $postId) {
            $postId = (int) $postId;
            $summary['checked']++;

            $content = (string) get_post_field('post_content', $postId);

            if (!wpape_backfill_content_has_youtube($content)) {
                continue;
            }

            $summary['matched']++;
            $alreadyMarked = wpape_backfill_has_video_meta($postId);
            $wouldUpdate = !$alreadyMarked;

            $matchedPosts[] = [
                'id' => $postId,
                'date' => get_the_date('Y-m-d H:i:s', $postId),
                'title' => get_the_title($postId),
                'edit_link' => get_edit_post_link($postId, ''),
                'already_marked' => $alreadyMarked,
                'would_update' => $wouldUpdate,
            ];

            if ($alreadyMarked) {
                $summary['already_marked']++;
                continue;
            }

            if (!$isDryRun) {
                wpape_backfill_set_has_video($postId);
            }

            $summary['updated']++;
        }

        $maxPages = (int) $query->max_num_pages;
        wp_reset_postdata();
        $paged++;
    } while ($paged <= $maxPages);

    if (!$isDryRun) {
        update_option($optionName, current_time('mysql'), false);
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Backfill hasVideo</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; max-width: 760px; }
        code { background: #f0f0f1; padding: 2px 5px; }
        button { padding: 8px 14px; }
        .warning { border-left: 4px solid #d63638; padding: 10px 14px; background: #fcf0f1; }
        .result { border-left: 4px solid #00a32a; padding: 10px 14px; background: #edfaef; }
        table { border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #c3c4c7; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f6f7f7; }
    </style>
</head>
<body>
    <h1>Backfill hasVideo</h1>
    <p>Esta herramienta busca posts publicados desde <code><?php echo esc_html($startDate); ?></code> hasta <code><?php echo esc_html($endDate); ?></code> inclusive y marca <code>hasVideo</code> cuando encuentra links o embeds de YouTube.</p>
    <p class="warning">Ejecutar una sola vez y borrar este archivo del servidor al terminar.</p>

    <?php if ($summary !== null) : ?>
        <div class="result">
            <p><strong><?php echo $isDryRun ? 'Simulacion finalizada' : 'Actualizacion finalizada'; ?></strong></p>
            <p>Posts revisados: <?php echo esc_html((string) $summary['checked']); ?></p>
            <p>Posts con YouTube detectado: <?php echo esc_html((string) $summary['matched']); ?></p>
            <p>Posts ya marcados previamente: <?php echo esc_html((string) $summary['already_marked']); ?></p>
            <p>Posts <?php echo $isDryRun ? 'que se actualizarian' : 'actualizados'; ?>: <?php echo esc_html((string) $summary['updated']); ?></p>
        </div>

        <?php if (!empty($matchedPosts)) : ?>
            <h2>Posts con YouTube detectado</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Titulo</th>
                        <th>hasVideo actual</th>
                        <th><?php echo $isDryRun ? 'Accion simulada' : 'Accion realizada'; ?></th>
                        <th>Editar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($matchedPosts as $matchedPost) : ?>
                        <tr>
                            <td><?php echo esc_html((string) $matchedPost['id']); ?></td>
                            <td><?php echo esc_html((string) $matchedPost['date']); ?></td>
                            <td><?php echo esc_html((string) $matchedPost['title']); ?></td>
                            <td><?php echo $matchedPost['already_marked'] ? 'Si' : 'No'; ?></td>
                            <td><?php echo $matchedPost['would_update'] ? ($isDryRun ? 'Se actualizaria' : 'Actualizado') : 'Sin cambios'; ?></td>
                            <td>
                                <?php if (!empty($matchedPost['edit_link'])) : ?>
                                    <a href="<?php echo esc_url((string) $matchedPost['edit_link']); ?>" target="_blank" rel="noopener noreferrer">Abrir editor</a>
                                <?php else : ?>
                                    No disponible
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ((int) $summary['matched'] === 0) : ?>
            <p>No se detectaron posts con contenido de YouTube.</p>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post">
        <?php wp_nonce_field($nonceAction); ?>
        <p>
            <label>
                <input type="checkbox" name="wpape_dry_run" value="1" checked>
                Solo simular, no actualizar datos
            </label>
        </p>
        <p>
            <button type="submit" name="wpape_run_backfill" value="1">Ejecutar backfill</button>
        </p>
    </form>
</body>
</html>

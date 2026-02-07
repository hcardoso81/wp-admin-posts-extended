<?php

$selected = $_GET['admin_tag'] ?? '';

$tags = get_terms([
    'taxonomy' => 'post_tag',
    'hide_empty' => false,
]);
?>

<select name="admin_tag">
    <option value="">Todos los tags</option>
    <?php foreach ($tags as $tag): ?>
        <option value="<?= esc_attr($tag->slug) ?>" <?= selected($selected, $tag->slug) ?>>
            <?= esc_html($tag->name) ?>
        </option>
    <?php endforeach; ?>
</select>

<?php
/**
 * Tag filter for posts list
 */

$selected = isset($_GET['admin_tag']) ? sanitize_text_field($_GET['admin_tag']) : '';

$tags = get_terms([
    'taxonomy'   => 'post_tag',
    'hide_empty' => false,
]);
?>

<select name="admin_tag" id="filter-by-tag">
    <option value="">
        <?php esc_html_e('All tags', 'wp-admin-posts-extended'); ?>
    </option>

    <?php foreach ($tags as $tag) : ?>
        <option value="<?php echo esc_attr($tag->slug); ?>"
            <?php selected($selected, $tag->slug); ?>>
            <?php echo esc_html($tag->name); ?>
        </option>
    <?php endforeach; ?>
</select>

<?php
$selected = isset($_GET['admin_tag']) ? (array) $_GET['admin_tag'] : [];

$tags = get_terms([
    'taxonomy'   => 'post_tag',
    'hide_empty' => false,
]);
?>

<select
    name="admin_tag[]"
    id="filter-by-tag"
    multiple
    data-placeholder="Filtrar por etiquetas"
>
    <?php foreach ($tags as $tag) : ?>
        <option value="<?php echo esc_attr($tag->slug); ?>"
            <?php echo in_array($tag->slug, $selected) ? 'selected' : ''; ?>>
            <?php echo esc_html($tag->name); ?>
        </option>
    <?php endforeach; ?>
</select>
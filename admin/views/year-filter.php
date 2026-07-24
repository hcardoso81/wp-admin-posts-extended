<?php
$selectedYear = isset($_GET['admin_year']) ? absint($_GET['admin_year']) : 0;
$currentYear = (int) current_time('Y');
?>

<select name="admin_year" id="filter-by-year">
    <option value="">Todos los a&ntilde;os</option>
    <?php for ($year = $currentYear; $year >= 2016; $year--) : ?>
        <option value="<?php echo esc_attr((string) $year); ?>"
            <?php selected($selectedYear, $year); ?>>
            <?php echo esc_html((string) $year); ?>
        </option>
    <?php endfor; ?>
</select>

<?php
$selectedAuthor = isset($_GET['admin_author']) ? absint($_GET['admin_author']) : 0;

wp_dropdown_users([
    'name'              => 'admin_author',
    'id'                => 'filter-by-author',
    'selected'          => $selectedAuthor,
    'show_option_all'   => 'Todos los autores',
    'hide_if_only_one_author' => false,
    'capability'        => 'edit_posts',
]);

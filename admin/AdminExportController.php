<?php

namespace WPAdminPostsExtended\Admin;

use WPAdminPostsExtended\Infrastructure\WordPress\Request;
use WPAdminPostsExtended\Infrastructure\WordPress\WpPostRepository;

class AdminExportController
{
    public function register(): void
    {
        add_action('admin_init', [$this, 'handleExport']);
    }

    public function handleExport(): void
    {
        
        if (!is_admin()) {
            return;
        }

        if (!isset($_GET['post_type']) || $_GET['post_type'] !== 'post') {
            return;
        }
    
        if (!isset($_GET['export_posts'])) {
            return;
        }

        if (!current_user_can('edit_posts')) {
            return;
        }

        $criteria = Request::postCriteriaFromAdmin();
        $posts    = (new WpPostRepository())->findByCriteria($criteria);

        $this->exportCsv($posts);
        exit;
    }

    private function exportCsv(array $posts): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=posts-export.csv');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Fecha',
            'Título',
            'Categorías',
            'Etiquetas',
        ]);

        foreach ($posts as $post) {
            fputcsv($output, [
                get_the_date('Y-m-d', $post),
                $post->post_title,
                $this->terms($post->ID, 'category'),
                $this->terms($post->ID, 'post_tag'),
            ]);
        }

        fclose($output);
    }

    private function terms(int $postId, string $taxonomy): string
    {
        $terms = get_the_terms($postId, $taxonomy);

        if (empty($terms) || is_wp_error($terms)) {
            return '';
        }

        return implode(', ', wp_list_pluck($terms, 'name'));
    }
}

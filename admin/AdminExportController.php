<?php

namespace WPAdminPostsExtended\Admin;

use WPAdminPostsExtended\Infrastructure\WordPress\Request;
use WPAdminPostsExtended\Infrastructure\WordPress\WpPostRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AdminExportController
{
    public function register(): void
    {
        add_action('admin_init', [$this, 'handleExport']);
    }

    private function buildFilename(): string
    {
        $date = date('Y-m-d');
        $parts = [$date];

        if (!empty($_GET['admin_tag'])) {
            $tags = array_map('sanitize_text_field', (array) $_GET['admin_tag']);
            $parts[] = 'tags-' . implode('-', $tags);
        }

        if (!empty($_GET['admin_author'])) {
            $authorId = absint($_GET['admin_author']);
            $author = $authorId ? get_userdata($authorId) : false;

            if ($author) {
                $parts[] = 'autor-' . sanitize_title($author->display_name);
            }
        }

        if (!empty($_GET['admin_year'])) {
            $year = absint($_GET['admin_year']);

            if ($year >= 2016 && $year <= (int) current_time('Y')) {
                $parts[] = 'ano-' . $year;
            }
        }

        $parts[] = 'posts';

        return implode('-', $parts) . '.xlsx';
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

        while (ob_get_level()) {
            ob_end_clean();
        }

        $criteria = Request::postCriteriaFromAdmin();
        $posts = (new WpPostRepository())->findByCriteria($criteria);

        $this->exportExcel($posts);
        exit;
    }

    private function exportExcel(array $posts): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Posts');

        $headers = ['Fecha', 'Titulo', 'Link', 'Estado LinkedIn', 'Fuente', 'Contiene Videos', 'Categorias', 'Etiquetas'];
        $sheet->fromArray([$headers], null, 'A1');
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;

        foreach ($posts as $post) {
            $sheet->setCellValue('A' . $row, get_the_date('d/m/Y', $post));
            $sheet->setCellValue('B' . $row, $post->post_title);

            $sheet->setCellValue('C' . $row, get_permalink($post));
            $sheet->getCell('C' . $row)
                ->getHyperlink()
                ->setUrl(get_permalink($post));

            $cell = 'D' . $row;
            $sheet->setCellValue($cell, $this->linkedinStatusLabel($post->ID));
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $fuenteValue = get_post_meta($post->ID, 'fuente', true);

            if (empty($fuenteValue) || $fuenteValue === 'comunicado_prensa') {
                $fuenteLabel = 'Comunicado de prensa';
            } else {
                $fuenteLabel = 'Nota original';
            }

            $sheet->setCellValue('E' . $row, $fuenteLabel);
            $sheet->setCellValue('F' . $row, $this->hasVideoLabel($post->ID));
            $sheet->setCellValue('G' . $row, $this->terms($post->ID, 'category'));
            $sheet->setCellValue('H' . $row, $this->terms($post->ID, 'post_tag'));

            $row++;
        }

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $this->buildFilename() . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    private function terms(int $postId, string $taxonomy): string
    {
        $terms = get_the_terms($postId, $taxonomy);

        if (empty($terms) || is_wp_error($terms)) {
            return '';
        }

        return implode(', ', wp_list_pluck($terms, 'name'));
    }

    private function linkedinStatusLabel(int $postId): string
    {
        $status = get_post_meta($postId, '_linkedin_status', true);

        $labels = [
            'pending' => 'Pendiente',
            'published' => 'Publicado',
            'manual_published' => 'Publicado manualmente',
            'scheduled' => 'Programado para publicar',
        ];

        if (is_string($status) && isset($labels[$status])) {
            return $labels[$status];
        }

        $posted = get_post_meta($postId, '_linkedin_posted', true);

        return !empty($posted) ? $labels['published'] : $labels['pending'];
    }

    private function hasVideoLabel(int $postId): string
    {
        $value = get_post_meta($postId, 'hasVideo', true);

        if (is_array($value)) {
            return in_array('1', $value, true) || in_array(1, $value, true) ? 'Si' : 'No';
        }

        return $value === '1' || $value === 1 || $value === true ? 'Si' : 'No';
    }
}

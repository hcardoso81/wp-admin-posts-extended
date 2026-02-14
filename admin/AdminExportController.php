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
            $tag = sanitize_text_field($_GET['admin_tag']);
            $tag = str_replace(' ', '-', strtolower($tag));
            $parts[] = 'tag-' . $tag;
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

        // Limpia cualquier buffer previo
        while (ob_get_level()) {
            ob_end_clean();
        }

        $criteria = Request::postCriteriaFromAdmin();
        $posts    = (new WpPostRepository())->findByCriteria($criteria);

        $this->exportExcel($posts);
        exit;
    }

    private function exportExcel(array $posts): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Posts');

        // Encabezados
        $headers = ['Fecha', 'Título', 'Link', 'Publicado en LinkedIn', 'Fuente', 'Categorías', 'Etiquetas'];
        $sheet->fromArray([$headers], null, 'A1');

        // Estilo headers
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        $row = 2;

        foreach ($posts as $post) {
            $sheet->setCellValue('A' . $row, get_the_date('Y-m-d', $post));
            $sheet->setCellValue('B' . $row, $post->post_title);

            // Link clickeable
            $sheet->setCellValue('C' . $row, get_permalink($post));
            $sheet->getCell('C' . $row)
                ->getHyperlink()
                ->setUrl(get_permalink($post));

            $posted = get_post_meta($post->ID, '_linkedin_posted', true);
            if (!empty($posted)) {
                $cell = 'D' . $row;

                $sheet->setCellValue($cell, '✔');

                $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle($cell)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }

            $fuenteValue = get_post_meta($post->ID, 'fuente', true);

            if ($fuenteValue === 'comunicado_prensa') {
                $fuenteLabel = 'Comunicado de prensa';
            } else {
                $fuenteLabel = 'Nota original';
            }

            $sheet->setCellValue('E' . $row, $fuenteLabel);

            $sheet->setCellValue('F' . $row, $this->terms($post->ID, 'category'));
            $sheet->setCellValue('G' . $row, $this->terms($post->ID, 'post_tag'));

            $row++;
        }

        // Auto ancho de columnas
        foreach (range('A', 'G') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Headers HTTP
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
}

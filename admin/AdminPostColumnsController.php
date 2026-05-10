<?php

namespace WPAdminPostsExtended\Admin;

class AdminPostColumnsController
{
    public function register(): void
    {
        add_filter('manage_post_posts_columns', [$this, 'addColumns']);
        add_action('manage_post_posts_custom_column', [$this, 'renderColumn'], 10, 2);
    }

    public function addColumns(array $columns): array
    {
        $updatedColumns = [];

        foreach ($columns as $key => $label) {
            $updatedColumns[$key] = $label;

            if ($key === 'title') {
                $updatedColumns['has_video'] = 'Contiene Videos';
            }
        }

        if (!isset($updatedColumns['has_video'])) {
            $updatedColumns['has_video'] = 'Contiene Videos';
        }

        return $updatedColumns;
    }

    public function renderColumn(string $column, int $postId): void
    {
        if ($column !== 'has_video') {
            return;
        }

        echo esc_html($this->hasVideo($postId) ? 'Si' : 'No');
    }

    private function hasVideo(int $postId): bool
    {
        $value = get_post_meta($postId, 'hasVideo', true);

        if (is_array($value)) {
            return in_array('1', $value, true) || in_array(1, $value, true);
        }

        return $value === '1' || $value === 1 || $value === true;
    }
}

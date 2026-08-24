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
        unset($columns['has_video']);

        $columns['has_video'] = '<span class="dashicons dashicons-video-alt3 wpape-video-column-heading" title="Contiene Videos" aria-label="Contiene Videos"></span>';

        return $columns;
    }

    public function renderColumn(string $column, int $postId): void
    {
        if ($column !== 'has_video') {
            return;
        }

        $hasVideo = $this->hasVideo($postId);
        $label = $hasVideo ? 'Contiene video' : 'No contiene video';
        $class = $hasVideo ? 'wpape-column-icon--ok' : 'wpape-column-icon--empty';
        $icon = $hasVideo ? 'dashicons-yes-alt' : 'dashicons-dismiss';

        printf(
            '<span class="wpape-column-icon %1$s" title="%2$s"><span class="dashicons %3$s" aria-hidden="true"></span><span class="screen-reader-text">%2$s</span></span>',
            esc_attr($class),
            esc_html($label),
            esc_attr($icon)
        );
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

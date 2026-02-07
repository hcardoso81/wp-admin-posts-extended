<?php

namespace WPAdminPostsExtended\Admin;

use WPAdminPostsExtended\Infrastructure\WordPress\Request;

class AdminFiltersController
{
    public function register(): void
    {
        add_action('restrict_manage_posts', [$this, 'renderFilters']);
        add_action('pre_get_posts', [$this, 'applyFilters']);
    }

    public function renderFilters(): void
    {
        global $typenow;

        if ($typenow !== 'post') {
            return;
        }

        require __DIR__ . '/views/tag-filter.php';
    }

    public function applyFilters($query): void
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        $criteria = Request::postCriteriaFromAdmin();

        if ($criteria->tag()) {
            $query->set('tag', $criteria->tag());
        }

        if ($criteria->category()) {
            $query->set('cat', $criteria->category());
        }

        if ($criteria->date()) {
            $query->set('m', $criteria->date());
        }

        if ($criteria->search()) {
            $query->set('s', $criteria->search());
        }
    }
}

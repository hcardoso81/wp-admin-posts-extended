<?php

namespace WPAdminPostsExtended\Admin;

use WP_Query;
use WPAdminPostsExtended\Infrastructure\WordPress\Request;
use WPAdminPostsExtended\Infrastructure\WordPress\AdminQueryModifier;

class AdminFiltersController
{
    private AdminQueryModifier $queryModifier;   
     
    public function __construct()
    {
        $this->queryModifier = new AdminQueryModifier();
    }

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

    public function applyFilters(WP_Query $query): void
    {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }
        
        if ($query->get('post_type') !== 'post') {
            return;
        }

        $criteria = Request::postCriteriaFromAdmin();
        $this->queryModifier->apply($criteria, $query);
    }
}

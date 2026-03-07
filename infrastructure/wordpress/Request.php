<?php

namespace WPAdminPostsExtended\Infrastructure\WordPress;

use WPAdminPostsExtended\Domain\PostCriteria;

class Request
{
    public static function postCriteriaFromAdmin(): PostCriteria
    {
        $tags = null;

        if (!empty($_GET['admin_tag'])) {
            $tags = array_map(
                'sanitize_text_field',
                (array) $_GET['admin_tag']
            );
        }

        $category = isset($_GET['cat']) && $_GET['cat'] !== ''
            ? (string) $_GET['cat']
            : null;

        $date = isset($_GET['m']) && $_GET['m'] !== ''
            ? sanitize_text_field($_GET['m'])
            : null;

        $search = isset($_GET['s']) && $_GET['s'] !== ''
            ? sanitize_text_field($_GET['s'])
            : null;

        return new PostCriteria($tags, $category, $date, $search);
    }
}

<?php

namespace WPAdminPostsExtended\Infrastructure\WordPress;

use WP_Query;
use WPAdminPostsExtended\Domain\PostCriteria;

class WpPostRepository
{
    public function findByCriteria(PostCriteria $criteria): array
    {
        $query = new WP_Query([
            'post_type'      => 'post',
            'posts_per_page' => -1,
        ]);

        (new AdminQueryModifier())->apply($criteria, $query);

        return $query->posts;
    }
}

<?php

namespace WPAdminPostsExtended\Infrastructure\WordPress;

use WPAdminPostsExtended\Domain\PostCriteria;
use WPAdminPostsExtended\Domain\PostRepositoryInterface;

class WpPostRepository implements PostRepositoryInterface
{
    public function findByCriteria(PostCriteria $criteria): array
    {
        $args = [
            'post_type'      => 'post',
            'posts_per_page' => -1,
            'post_status'    => 'any',
        ];

        if ($criteria->search()) {
            $args['s'] = $criteria->search();
        }

        if ($criteria->date()) {
            $args['m'] = $criteria->date(); // yyyyMM
        }

        if ($criteria->category()) {
            $args['cat'] = $criteria->category();
        }

        if ($criteria->tag()) {
            $args['tag'] = $criteria->tag(); // slug
        }

        return get_posts($args);
    }
}

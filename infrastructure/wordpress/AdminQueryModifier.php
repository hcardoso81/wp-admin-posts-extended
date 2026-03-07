<?php

namespace WPAdminPostsExtended\Infrastructure\WordPress;

use WP_Query;
use WPAdminPostsExtended\Domain\PostCriteria;

class AdminQueryModifier
{
    public function apply(PostCriteria $criteria, WP_Query $query): void
    {
        if ($criteria->tags()) {
            $query->set('tax_query', [
                [
                    'taxonomy' => 'post_tag',
                    'field'    => 'slug',
                    'terms'    => $criteria->tags(),
                    'operator' => 'IN'
                ]
            ]);
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

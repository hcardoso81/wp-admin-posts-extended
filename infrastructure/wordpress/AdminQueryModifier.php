<?php

namespace WPAdminPostsExtended\Infrastructure\WordPress;

use WPAdminPostsExtended\Domain\PostCriteria;
use WP_Query;

class AdminQueryModifier
{
    public function apply(PostCriteria $criteria, WP_Query $query): void
    {
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

<?php

namespace WPAdminPostsExtended\Domain;

interface PostRepositoryInterface
{
    /**
     * @return array<int, mixed>
     */
    public function findByCriteria(PostCriteria $criteria): array;
}

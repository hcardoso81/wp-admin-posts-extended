<?php

namespace WPAdminPostsExtended\Domain;

class PostCriteria
{
    private ?array $tags;
    private ?string $category;
    private ?string $date;
    private ?string $search;

    public function __construct(
        ?array $tags = null,
        ?string $category = null,
        ?string $date = null,
        ?string $search = null
    ) {
        $this->tags = $tags;
        $this->category = $category;
        $this->date = $date;
        $this->search = $search;
    }

    public function tags(): ?array
    {
        return $this->tags;
    }

    public function category(): ?string
    {
        return $this->category;
    }

    public function date(): ?string
    {
        return $this->date;
    }

    public function search(): ?string
    {
        return $this->search;
    }
}
<?php

namespace WPAdminPostsExtended\Domain;

class PostCriteria
{
    private ?string $tag;
    private ?string $category;
    private ?string $date;
    private ?string $search;

    public function __construct(
        ?string $tag = null,
        ?string $category = null,
        ?string $date = null,
        ?string $search = null
    ) {
        $this->tag = $tag;
        $this->category = $category;
        $this->date = $date;
        $this->search = $search;
    }

    public function tag(): ?string
    {
        return $this->tag;
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

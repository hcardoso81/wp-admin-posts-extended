<?php

namespace WPAdminPostsExtended\Domain;

class PostCriteria
{
    private ?array $tags;
    private ?string $category;
    private ?string $date;
    private ?int $year;
    private ?string $search;
    private ?int $author;

    public function __construct(
        ?array $tags = null,
        ?string $category = null,
        ?string $date = null,
        ?string $search = null,
        ?int $author = null,
        ?int $year = null
    ) {
        $this->tags = $tags;
        $this->category = $category;
        $this->date = $date;
        $this->year = $year;
        $this->search = $search;
        $this->author = $author;
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

    public function year(): ?int
    {
        return $this->year;
    }

    public function author(): ?int
    {
        return $this->author;
    }
}

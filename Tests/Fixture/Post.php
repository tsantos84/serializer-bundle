<?php

/*
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\_NAMESPACE_;

/**
 * Class Post.
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class Post
{
    /** @var int */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $summary;

    /**
     * Post constructor.
     *
     * @param int    $id
     * @param string $title
     * @param string $summary
     */
    public function __construct(int $id, string $title, string $summary)
    {
        $this->id = $id;
        $this->title = $title;
        $this->summary = $summary;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary(string $summary): void
    {
        $this->summary = $summary;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:06
 */

namespace Model\Entity;


class Post extends Entity
{
    protected $authorId = null;
    protected $lastEditorId = null;
    protected $creationDate = null;
    protected $lastModificationDate = null;
    protected $markdown = false;
    protected $title = null;
    protected $excerpt = null;
    protected $content = null;

    // Associated properties
    protected $tags = [];
    protected $categories = [];
    protected $authorName = null;
    protected $editorName = null;

    /**
     * @return null
     */
    public function getAuthorName()
    {
        return $this->authorName;
    }

    /**
     * @param null $authorName
     */
    public function setAuthorName($authorName): void
    {
        $this->authorName = $authorName;
    }

    /**
     * @return null
     */
    public function getEditorName()
    {
        return $this->editorName;
    }

    /**
     * @param null $editorName
     */
    public function setEditorName($editorName): void
    {
        $this->editorName = $editorName;
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param array $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return int
     */
    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     */
    public function setAuthorId(int $authorId): void
    {
        $this->authorId = $authorId;
    }

    /**
     * @return int
     */
    public function getLastEditorId(): ?int
    {
        return $this->lastEditorId;
    }

    /**
     * @param int $lastEditorId
     */
    public function setLastEditorId(?int $lastEditorId): void
    {
        $this->lastEditorId = $lastEditorId;
    }

    /**
     * @return string
     */
    public function getCreationDate(): ?string
    {
        return $this->creationDate;
    }

    /**
     * @param string $creationDate
     */
    public function setCreationDate(string $creationDate): void
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return string
     */
    public function getLastModificationDate(): ?string
    {
        return $this->lastModificationDate;
    }

    /**
     * @param string $lastModificationDate
     */
    public function setLastModificationDate(?string $lastModificationDate): void
    {
        $this->lastModificationDate = $lastModificationDate;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
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
    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    /**
     * @param string $excerpt
     */
    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
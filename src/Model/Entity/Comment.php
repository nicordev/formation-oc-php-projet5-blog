<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 06:35
 */

namespace Model\Entity;


class Comment extends Entity
{
    public $parentId = null;
    public $postId = null;
    public $authorId = null;
    public $lastEditorId = null;
    public $creationDate = '';
    public $lastModificationDate = '';
    public $content = '';

    /**
     * @return int
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     */
    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @return int
     */
    public function getPostId(): ?int
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
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
    public function setLastEditorId(int $lastEditorId): void
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
    public function setLastModificationDate(string $lastModificationDate): void
    {
        $this->lastModificationDate = $lastModificationDate;
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

}
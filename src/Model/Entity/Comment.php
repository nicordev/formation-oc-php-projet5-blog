<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 06:35
 */

namespace Model\Entity;


class Comment extends MemberCreation
{
    protected $parentId = null;
    protected $postId = null;
    protected $approved = false;

    protected $author = null;
    protected $lastEditor = null;
    protected $postTitle = null;
    protected $parent = null;
    protected $children = [];

    /**
     * @return Comment|null
     */
    public function getParent(): ?Comment
    {
        return $this->parent;
    }

    /**
     * @param Comment|null $parent
     */
    public function setParent(?Comment $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * Add a child to the array of children
     *
     * @param Comment $comment
     */
    public function addAChild(Comment $comment): void
    {
        $this->children[] = $comment;
    }

    /**
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param array $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    /**
     * @return string|null
     */
    public function getLastEditor(): ?string
    {
        return $this->lastEditor;
    }

    /**
     * @param string|null $lastEditor
     */
    public function setLastEditor(?string $lastEditor): void
    {
        $this->lastEditor = $lastEditor;
    }

    /**
     * @return string|null
     */
    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    /**
     * @param string|null $postTitle
     */
    public function setPostTitle(?string $postTitle): void
    {
        $this->postTitle = $postTitle;
    }

    /**
     * @return string|null
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * @param string|null $author
     */
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

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
     * @return bool
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * @param bool $approved
     */
    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }
}

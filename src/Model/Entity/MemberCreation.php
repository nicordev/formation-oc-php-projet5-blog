<?php

namespace Model\Entity;


class MemberCreation extends Entity
{
    protected $authorId = null;
    protected $lastEditorId = null;
    protected $creationDate = null;
    protected $lastModificationDate = null;
    protected $content = null;

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
    public function setAuthorId(?int $authorId): void
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
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(?string $content): void
    {
        $this->content = $content;
    }
}
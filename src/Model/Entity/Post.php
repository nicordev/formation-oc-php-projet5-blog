<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:06
 */

namespace Model\Entity;


class Post extends MemberCreation
{
    protected $markdown = false;
    protected $title = null;
    protected $excerpt = null;

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
     * @param bool|null $namesOnly
     * @return array
     */
    public function getCategories(?bool $namesOnly = false): array
    {
        if ($namesOnly) {
            $names = [];
            foreach ($this->categories as $category) {
                $names[] = $category->getName();
            }
            return $names;
        }
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
     * @return bool
     */
    public function isMarkdown(): bool
    {
        return $this->markdown;
    }

    /**
     * @param bool $markdown
     */
    public function setMarkdown(bool $markdown): void
    {
        $this->markdown = $markdown;
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
     * @param bool|null $namesOnly
     * @return array
     */
    public function getTags(?bool $namesOnly = false): array
    {
        if ($namesOnly) {
            $names = [];
            foreach ($this->tags as $tag) {
                $names[] = $tag->getName();
            }
            return $names;
        }
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

<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Tag;

class TagManager extends Manager
{
    /**
     * Add a new tag in the database
     *
     * @param Tag $newTag
     * @throws Exception
     */
    public function add($newTag): void
    {
        parent::add($newTag);
    }

    /**
     * Edit a tag in the database
     *
     * @param Tag $modifiedTag
     * @throws Exception
     */
    public function edit($modifiedTag): void
    {
        parent::edit($modifiedTag);
    }

    /**
     * Delete a tag in the database
     *
     * @param int $tagId
     * @throws Exception
     */
    public function delete(int $tagId): void
    {
        parent::delete($tagId);
    }

    /**
     * Get a tag from the database
     *
     * @param int $tagId
     * @return Tag
     * @throws Exception
     */
    public function get(int $tagId): Tag
    {
        return parent::get($tagId);
    }

    /**
     * Get all tags from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        return parent::getAll();
    }
}
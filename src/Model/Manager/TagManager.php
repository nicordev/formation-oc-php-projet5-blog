<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Tag;
use PDO;

class TagManager extends Manager
{
    /**
     * TagManager constructor.
     */
    public function __construct()
    {
        $this->tableName = 'bl_tag';
        $this->fields = [
            'id' => 'tag_id',
            'name' => 'tag_name'
        ];

        parent::__construct();
    }

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

    /**
     * Check if a tag is new
     *
     * @param Tag $newTag
     * @return bool
     */
    public function isNewTag(Tag $newTag): bool
    {
        $tags = $this->getAll();

        if (!empty($tags)) {
            foreach ($tags as $tag) {
                if ($tag->getName() === $newTag->getName()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get the id of a tag from its name
     *
     * @param string $tagName
     * @return mixed
     * @throws \Application\Exception\BlogException
     */
    public function getId(string $tagName)
    {
        $query = 'SELECT tag_id FROM bl_tag WHERE tag_name = :tag';
        $requestId = $this->query($query, [
            'tag' => $tagName
        ]);

        $id = (int) $requestId->fetch(PDO::FETCH_NUM)[0];

        return $id;
    }
}
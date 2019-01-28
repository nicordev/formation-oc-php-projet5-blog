<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Tag;
use PDO;

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
        $query = 'INSERT INTO bl_tag(tag_name)
            VALUES (?)';

        $requestAdd = $this->database->prepare($query);
        if (!$requestAdd->execute([$newTag->getName()])) {
            throw new Exception('Error when trying to add the new tag in the database.');
        }
    }

    /**
     * Edit a tag in the database
     *
     * @param Tag $modifiedTag
     * @throws Exception
     */
    public function edit($modifiedTag): void
    {
        $query = 'UPDATE bl_tag
            SET tag_name = :tagName
            WHERE tag_id = :id';

        $requestEdit = $this->database->prepare($query);
        if (!$requestEdit->execute([
            'id' => $modifiedTag->getId(),
            'tagName' => $modifiedTag->getName()
        ])) {
            throw new Exception('Error when trying to edit a tag in the database. Tag id:' . $modifiedTag->getId());
        }
    }

    /**
     * Delete a tag in the database
     *
     * @param int $tagId
     * @throws Exception
     */
    public function delete(int $tagId): void
    {
        $query = 'DELETE FROM bl_tag WHERE tag_id = ?';

        $requestDelete = $this->database->prepare($query);
        if (!$requestDelete->execute([$tagId])) {
            throw new Exception('Error when trying to delete a tag in the database. Tag id:' . $tagId);
        }
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
        $query = "SELECT * FROM bl_tag WHERE tag_id = ?";

        $requestATag = $this->database->prepare($query);
        if (!$requestATag->execute([$tagId])) {
            throw new Exception('Error when trying to get a tag from the database. Tag id:' . $tagId);
        }
        $theTagData = $requestATag->fetch(PDO::FETCH_ASSOC);
        if (!$theTagData) {
            throw new Exception('Error when trying to get a tag. Tag id: ' . $tagId);
        }

        return self::createATagFromDatabaseData($theTagData);
    }

    /**
     * Get all tags from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        $tags = [];
        $query = "SELECT * FROM bl_tag";

        $requestAllTags = $this->database->query($query);
        $tagsData = $requestAllTags->fetchAll(PDO::FETCH_ASSOC);

        foreach ($tagsData as $tagsDatum) {
            $tags[] = self::createATagFromDatabaseData($tagsDatum);
        }

        return $tags;
    }

    // Private

    /**
     * @param array $data
     * @return Tag
     */
    private static function createATagFromDatabaseData(array $data): Tag
    {
        $attributes = [
            'id' => $data['tag_id'],
            'name' => $data['tag_name']
        ];

        return new Tag($attributes);
    }
}
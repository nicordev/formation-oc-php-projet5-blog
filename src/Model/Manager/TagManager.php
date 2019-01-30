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
    public function add(Tag $newTag): void
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
    public function edit(Tag $modifiedTag): void
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
     */
    public function getId(string $tagName)
    {
        $query = 'SELECT tag_id FROM bl_tag WHERE tag_name = :tag';
        $requestId = $this->database->prepare($query);
        $requestId->execute([
            'tag' => $tagName
        ]);

        $id = (int) $requestId->fetch(PDO::FETCH_NUM)[0];

        return $id;
    }

    /**
     * @param array $data
     * @return Tag
     */
    public static function createATagFromDatabaseData(array $data): Tag
    {
        $attributes = [
            'id' => $data['tag_id'],
            'name' => $data['tag_name']
        ];

        return new Tag($attributes);
    }
}
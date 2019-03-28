<?php

namespace Model\Manager;


use Model\Entity\Tag;
use PDO;

class TagManager extends Manager
{
    /**
     * TagManager constructor.
     * @throws \Application\Exception\HttpException
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
     * Check if a tag is new
     *
     * @param Tag $newTag
     * @return bool
     * @throws \Application\Exception\HttpException
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
     * @throws \Application\Exception\HttpException
     */
    public function getId(string $tagName)
    {
        $query = 'SELECT tag_id FROM bl_tag WHERE tag_name = :tag';
        $requestId = $this->query($query, [
            'tag' => $tagName
        ]);

        return (int) $requestId->fetch(PDO::FETCH_NUM)[0];
    }
}

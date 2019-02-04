<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Category;
use PDO;

class CategoryManager extends Manager
{
    /**
     * CategoryManager constructor.
     */
    public function __construct()
    {
        $this->tableName = 'bl_category';
        $this->fields = [
            'id' => 'cat_id',
            'name' => 'cat_name'
        ];

        parent::__construct();
    }

    /**
     * Add a new category in the database
     *
     * @param Category $newCategory
     * @throws Exception
     */
    public function add($newCategory): void
    {
        parent::add($newCategory);

        // Associate tags and category
        $tags = $newCategory->getTags();
        if (!empty($tags)) {
            $newCategory->setId($this->getLastId());
            $this->associateCategoryAndTags($newCategory, $tags);
        }
    }

    /**
     * Edit a category in the database
     *
     * @param Category $modifiedCategory
     * @throws Exception
     */
    public function edit($modifiedCategory): void
    {
        parent::edit($modifiedCategory);

        // Associate tags and category
        $tags = $modifiedCategory->getTags();
        if (!empty($tags)) {
            $this->associateCategoryAndTags($modifiedCategory, $tags);
        }
    }

    /**
     * Delete a category in the database
     *
     * @param int $categoryId
     * @throws Exception
     */
    public function delete(int $categoryId): void
    {
        parent::delete($categoryId);
    }

    /**
     * Get a category from the database
     *
     * @param int $categoryId
     * @return Category
     * @throws Exception
     */
    public function get(int $categoryId): Category
    {
        $category = parent::get($categoryId);

        $associatedTags = $this->getTagsOfACategory($category->getId());
        $category->setTags($associatedTags);

        return $category;
    }

    /**
     * Get associated tags of a category
     *
     * @param int $categoryId
     * @return array
     */
    public function getTagsOfACategory(int $categoryId)
    {
        $tags = [];

        $query = 'SELECT bl_tag.* FROM bl_tag
            INNER JOIN bl_category_tag
                ON tag_id = ct_tag_id_fk
            INNER JOIN bl_category ON ct_category_id_fk = cat_id
            WHERE cat_id = :categoryId';

        $requestTags = $this->database->prepare($query);
        $requestTags->execute([
            'categoryId' => $categoryId
        ]);
        while ($tagData = $requestTags->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = TagManager::createATagFromDatabaseData($tagData);
        }

        return $tags;
    }

    /**
     * Get all categories from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        $categories = parent::getAll();

        // Get tags
        foreach ($categories as $category) {
            $category->setTags($this->getTagsOfACategory($category->getId()));
        }

        return $categories;
    }
    
    // Private

    /**
     * Fill the table bl_category_tag
     *
     * @param Category $category
     * @param array $tags
     */
    private function associateCategoryAndTags(Category $category, array $tags)
    {
        // Delete
        $query = 'DELETE FROM bl_category_tag WHERE ct_category_id_fk = :categoryId';
        $requestDelete = $this->database->prepare($query);
        $requestDelete->execute([
            'categoryId' => $category->getId()
        ]);

        // Add
        $query = 'INSERT INTO bl_category_tag(ct_category_id_fk, ct_tag_id_fk)
                VALUES (:categoryId, :tagId)';
        $requestAdd = $this->database->prepare($query);

        foreach ($tags as $tag) {
            $requestAdd->execute([
                'categoryId' => $category->getId(),
                'tagId' => $tag->getId()
            ]);
        }
    }
}
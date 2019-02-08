<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Category;
use Model\Entity\Post;
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
     * Get the categories where we can find the post
     *
     * @param int $postId
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getCategoriesFromPostId(int $postId): array
    {
        $categories = [];

        $query = 'SELECT DISTINCT ct_category_id_fk FROM bl_category_tag
            WHERE ct_tag_id_fk IN (
                SELECT pt_tag_id_fk FROM bl_post_tag
                WHERE pt_post_id_fk = :id
            )';

        $requestPostId = $this->query($query, [
            'id' => $postId
        ]);

        $categoryIds = $requestPostId->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categoryIds as $categoryId) {
            $categories[] = parent::get($categoryId['ct_category_id_fk']);
        }

        return $categories;
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

        $requestTags = $this->query($query, [
            'categoryId' => $categoryId
        ]);

        while ($tagData = $requestTags->fetch(PDO::FETCH_ASSOC)) {
            $tags[] = $this->createEntityFromTableData($tagData, 'Tag');
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

    /**
     * @param array $data
     * @return Category
     */
    public static function createACategoryFromDatabaseData(array $data): Category
    {
        $attributes = [
            'id' => $data['cat_id'],
            'name' => $data['cat_name']
        ];

        return new Category($attributes);
    }
    
    // Private

    /**
     * Fill the table bl_category_tag
     *
     * @param Category $category
     * @param array $tags
     * @throws \Application\Exception\BlogException
     */
    private function associateCategoryAndTags(Category $category, array $tags)
    {
        // Delete
        $query = 'DELETE FROM bl_category_tag WHERE ct_category_id_fk = :categoryId';
        $requestDelete = $this->query($query, [
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
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

        return $category;
    }

    /**
     * Get a category from its name
     *
     * @param string $categoryName
     * @return Category|null
     * @throws \Application\Exception\HttpException
     */
    public function getFromName(string $categoryName): ?Category
    {
        $query = 'SELECT * FROM bl_category WHERE cat_name = :categoryName';
        $requestCategory = $this->query($query, ['categoryName' => $categoryName]);
        $categoryData = $requestCategory->fetch(PDO::FETCH_ASSOC);

        return $this->createEntityFromTableData($categoryData);
    }

    /**
     * Get the categories where we can find the post
     *
     * @param int $postId
     * @return array
     * @throws \Application\Exception\HttpException
     */
    public function getCategoriesFromPostId(int $postId): array
    {
        $categories = [];

        $query = 'SELECT DISTINCT ct_category_id_fk FROM bl_post_category
            WHERE ct_post_id_fk = :postId';

        $requestPostId = $this->query($query, [
            'postId' => $postId
        ]);

        $categoryIds = $requestPostId->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categoryIds as $categoryId) {
            $categories[] = $this->get($categoryId['ct_category_id_fk']);
        }

        return $categories;
    }

    /**
     * Get all categories from the database
     *
     * @return array
     * @throws \Application\Exception\HttpException
     */
    public function getAll(): array
    {
        $categories = parent::getAll();

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


}
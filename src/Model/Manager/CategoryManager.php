<?php

namespace Model\Manager;


use Model\Entity\Category;
use PDO;

class CategoryManager extends Manager
{
    /**
     * CategoryManager constructor.
     *
     * @throws \Application\Exception\HttpException
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

        $query = 'SELECT DISTINCT pc_category_id_fk FROM bl_post_category
            WHERE pc_post_id_fk = :postId';

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
}

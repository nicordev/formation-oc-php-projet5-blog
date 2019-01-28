<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Category;
use PDO;

class CategoryManager extends Manager
{
    /**
     * Add a new category in the database
     *
     * @param Category $newCategory
     * @throws Exception
     */
    public function add(Category $newCategory): void
    {
        $query = 'INSERT INTO bl_category(cat_name)
            VALUES (?)';

        $requestAdd = $this->database->prepare($query);
        if (!$requestAdd->execute([$newCategory->getName()])) {
            throw new Exception('Error when trying to add the new category in the database.');
        }
    }

    /**
     * Edit a category in the database
     *
     * @param Category $modifiedCategory
     * @throws Exception
     */
    public function edit(Category $modifiedCategory): void
    {
        $query = 'UPDATE bl_category
            SET cat_name = :categoryName
            WHERE cat_id = :id';

        $requestEdit = $this->database->prepare($query);
        if (!$requestEdit->execute([
            'id' => $modifiedCategory->getId(),
            'categoryName' => $modifiedCategory->getName()
        ])) {
            throw new Exception('Error when trying to edit a category in the database. Category id:' . $modifiedCategory->getId());
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
        $query = 'DELETE FROM bl_category WHERE cat_id = ?';

        $requestDelete = $this->database->prepare($query);
        if (!$requestDelete->execute([$categoryId])) {
            throw new Exception('Error when trying to delete a category in the database. Category id:' . $categoryId);
        }
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
        $query = "SELECT * FROM bl_category WHERE cat_id = ?";

        $requestACategory = $this->database->prepare($query);
        if (!$requestACategory->execute([$categoryId])) {
            throw new Exception('Error when trying to get a category from the database. Category id:' . $categoryId);
        }
        $theCategoryData = $requestACategory->fetch(PDO::FETCH_ASSOC);
        if (!$theCategoryData) {
            throw new Exception('Error when trying to get a category. Category id: ' . $categoryId);
        }

        return self::createACategoryFromDatabaseData($theCategoryData);
    }

    /**
     * Get all categories from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        $categories = [];
        $query = "SELECT * FROM bl_category";

        $requestAllCategories = $this->database->query($query);
        $categoriesData = $requestAllCategories->fetchAll(PDO::FETCH_ASSOC);

        foreach ($categoriesData as $categoriesDatum) {
            $categories[] = self::createACategoryFromDatabaseData($categoriesDatum);
        }

        return $categories;
    }

    // Private

    /**
     * @param array $data
     * @return Category
     */
    private static function createACategoryFromDatabaseData(array $data): Category
    {
        $attributes = [
            'id' => $data['cat_id'],
            'name' => $data['cat_name']
        ];

        return new Category($attributes);
    }
}
<?php

namespace Model\Manager;


use Exception;
use Model\Entity\Category;

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
        return parent::get($categoryId);
    }

    /**
     * Get all categories from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        return parent::getAll();
    }
}
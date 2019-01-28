<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;

use Model\Entity\Comment;
use \Exception;

class CommentManager extends Manager
{

    /**
     * Add a new comment in the database
     *
     * @param Comment $newComment
     * @throws Exception
     */
    public function add($newComment): void
    {
        parent::add($newComment);
    }

    /**
     * Edit a comment in the database
     *
     * @param Comment $modifiedComment
     * @throws Exception
     */
    public function edit($modifiedComment): void
    {
        parent::edit($modifiedComment);
    }

    /**
     * Delete a comment in the database
     *
     * @param int $commentId
     * @throws Exception
     */
    public function delete(int $commentId): void
    {
        parent::delete($commentId);
    }

    /**
     * Get a comment from the database
     *
     * @param int $commentId
     * @return Comment
     * @throws Exception
     */
    public function get(int $commentId): Comment
    {
        return parent::get($commentId);
    }

    /**
     * Get all comments from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        return parent::getAll();
    }
}
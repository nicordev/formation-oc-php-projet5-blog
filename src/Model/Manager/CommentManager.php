<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;


use Model\Entity\Entity;
use Model\Entity\Comment;
use \Exception;
use \PDO;

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
        $query = 'INSERT INTO bl_comment(com_parent_id_fk, 
                       com_author_id_fk, 
                       com_creation_date, 
                       com_content)
            VALUES (:parentId, 
                :authorId, 
                NOW(), 
                :content)';

        $requestAdd = $this->database->prepare($query);
        if (!$requestAdd->execute([
            'parentId' => $newComment->getParentId() === null ? null : $newComment->getParentId(),
            'authorId' => $newComment->getAuthorId(),
            'content' => $newComment->getContent()
        ])) {
            throw new Exception('Error when trying to add the new comment in the database.');
        }
    }

    /**
     * Edit a comment in the database
     *
     * @param Comment $modifiedComment
     * @throws Exception
     */
    public function edit($modifiedComment): void
    {
        $query = 'UPDATE bl_comment
            SET com_parent_id_fk = :parentId,
                com_last_editor_id_fk = :lastEditorId,
                com_last_modification_date = NOW(),
                com_content = :content
            WHERE com_id = :id';

        $requestEdit = $this->database->prepare($query);
        if (!$requestEdit->execute([
            'parentId' => $modifiedComment->getParentId() === null ? null : $modifiedComment->getParentId(),
            'id' => $modifiedComment->getId(),
            'lastEditorId' => $modifiedComment->getLastEditorId(),
            'content' => $modifiedComment->getContent()
        ])) {
            throw new Exception('Error when trying to edit a comment in the database. Comment id:' . $modifiedComment->getId());
        }
    }

    /**
     * Delete a comment in the database
     *
     * @param int $commentId
     * @throws Exception
     */
    public function delete(int $commentId): void
    {
        $query = 'DELETE FROM bl_comment WHERE com_id = ?';

        $requestDelete = $this->database->prepare($query);
        if (!$requestDelete->execute([$commentId])) {
            throw new Exception('Error when trying to delete a comment in the database. Comment id:' . $commentId);
        }
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
        $query = "SELECT * FROM bl_comment WHERE com_id = ?";

        $requestAComment = $this->database->prepare($query);
        if (!$requestAComment->execute([$commentId])) {
            throw new Exception('Error when trying to get a comment from the database. Comment id:' . $commentId);
        }
        $theCommentData = $requestAComment->fetch(PDO::FETCH_ASSOC);
        if (!$theCommentData) {
            throw new Exception('Error when trying to get a comment. Comment id: ' . $commentId);
        }

        return self::createACommentFromDatabaseData($theCommentData);
    }

    /**
     * Get all comments from the database
     *
     * @return array
     */
    public function getAll(): array
    {
        $comments = [];
        $query = "SELECT * FROM bl_comment";

        $requestAllComments = $this->database->query($query);
        $commentsData = $requestAllComments->fetchAll(PDO::FETCH_ASSOC);

        foreach ($commentsData as $commentsDatum) {
            $comments[] = self::createACommentFromDatabaseData($commentsDatum);
        }

        return $comments;
    }

    // Private

    /**
     * @param array $data
     * @return Comment
     */
    private static function createACommentFromDatabaseData(array $data): Comment
    {
        $attributes = [
            'id' => $data['com_id'],
            'parentId' => $data['com_parent_id_fk'] === null ? null : $data['com_parent_id_fk'],
            'authorId' => $data['com_author_id_fk'],
            'lastEditorId' => $data['com_last_editor_id_fk'] === null ? null : $data['com_last_editor_id_fk'],
            'creationDate' => $data['com_creation_date'],
            'lastModificationDate' => $data['com_last_modification_date'] === null ? '' : $data['com_last_modification_date'],
            'content' => $data['com_content']
        ];

        return new Comment($attributes);
    }
}
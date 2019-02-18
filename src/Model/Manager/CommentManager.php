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
use PDO;

class CommentManager extends Manager
{

    public function __construct()
    {
        $this->tableName = 'bl_comment';
        $this->fields = [
            'id' => 'com_id',
            'postId' => 'com_post_id_fk',
            'authorId' => 'com_author_id_fk',
            'lastEditorId' => 'com_last_editor_id_fk',
            'creationDate' => 'com_creation_date',
            'lastModificationDate' => 'com_last_modification_date',
            'content' => 'com_content'
        ];

        parent::__construct();
    }

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
     * @throws \Application\Exception\BlogException
     */
    public function getAll(): array
    {
        return parent::getAll();
    }

    /**
     * Get the comments of a post
     *
     * @param int $postId
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getFromPost(int $postId): array
    {
        $comments = [];
        $query = 'SELECT * FROM bl_comment
            WHERE com_post_id_fk = :postId';

        $requestComments = $this->query($query, ['postId' => $postId]);

        while ($commentData = $requestComments->fetch(PDO::FETCH_ASSOC)) {
            $comment = $this->createEntityFromTableData($commentData, 'Comment');
            $comment->setAuthor($this->getCommentAuthor($comment->getAuthorId()));
            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Get the name of the comment's author
     *
     * @param int $authorId
     * @return string|null
     * @throws \Application\Exception\BlogException
     */
    public function getCommentAuthor(int $authorId): ?string
    {
        $author = null;

        $query = 'SELECT m_name FROM bl_member
            WHERE m_id = :id';

        $requestAuthor = $this->query($query, ['id' => $authorId]);

        $author = $requestAuthor->fetch(PDO::FETCH_ASSOC);

        return $author['m_name'];
    }
}
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
            'parentId' => 'com_parent_id_fk',
            'postId' => 'com_post_id_fk',
            'authorId' => 'com_author_id_fk',
            'lastEditorId' => 'com_last_editor_id_fk',
            'creationDate' => 'com_creation_date',
            'lastModificationDate' => 'com_last_modification_date',
            'content' => 'com_content',
            'approved' => 'com_approved'
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
        $comment = parent::get($commentId);

        $comment->setAuthor($this->getCommentMember($comment->getAuthorId()));
        $comment->setPostTitle($this->getPostTitle($comment->getPostId()));
        if ($comment->getLastEditorId) {
            $comment->setLastEditor($this->getCommentMember($comment->getLastEditorId()));
        }

        return $comment;
    }

    /**
     * Get all comments from the database
     *
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getAll(): array
    {
        $comments = parent::getAll();

        foreach ($comments as $comment) {
            $comment->setAuthor($this->getCommentMember($comment->getAuthorId()));
            $comment->setPostTitle($this->getPostTitle($comment->getPostId()));
            if ($comment->getLastEditorId()) {
                $comment->setLastEditor($this->getCommentMember($comment->getLastEditorId()));
            }
            // Parent and children
            if ($comment->getParentId()) {
                $parent = $comments[$comment->getParentId()];
                $comment->setParent($parent);
                $parent->addAChild($comment);
            }
        }

        return $comments;
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
            $comment->setAuthor($this->getCommentMember($comment->getAuthorId()));
            $comment->setPostTitle($this->getPostTitle($comment->getPostId()));
            if ($comment->getLastEditorId()) {
                $comment->setLastEditor = $this->getCommentMember($comment->getLastEditorId());
            }
            $comments[$comment->getId()] = $comment;
        }

        // Parent and children
        self::fillParentsAndChildren($comments);

        return $comments;
    }

    /**
     * Get the name of the comment's author
     *
     * @param int $memberId
     * @return string|null
     * @throws \Application\Exception\BlogException
     */
    public function getCommentMember(int $memberId): ?string
    {
        $member = null;

        $query = 'SELECT m_name FROM bl_member
            WHERE m_id = :id';

        $requestAuthor = $this->query($query, ['id' => $memberId]);

        $member = $requestAuthor->fetch(PDO::FETCH_ASSOC);

        return $member['m_name'];
    }

    /**
     * Get the title of the post associated to the comment
     *
     * @param int $postId
     * @return mixed
     * @throws \Application\Exception\BlogException
     */
    public function getPostTitle(int $postId)
    {
        $postTitle = null;

        $query = 'SELECT p_title FROM bl_post
            WHERE p_id = :id';

        $requestAuthor = $this->query($query, ['id' => $postId]);

        $postTitle = $requestAuthor->fetch(PDO::FETCH_ASSOC);

        return $postTitle['p_title'];
    }

    /**
     * Get the comments written by a member
     *
     * @param int $memberId
     * @param bool $filterApproved
     * @param int|null $numberOfComments
     * @param int|null $start
     * @return array
     * @throws \Application\Exception\BlogException
     */
    public function getCommentsOfAMember(int $memberId, bool $filterApproved = true, ?int $numberOfComments = null, ?int $start = null)
    {
        $comments = [];

        $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM bl_comment WHERE com_author_id_fk = :memberId';
        if ($numberOfComments) {
            self::addLimitToQuery($query, $numberOfComments, $start);
        }
        $requestComments = $this->query($query, ['memberId' => $memberId]);

        if ($filterApproved) {
            while ($commentData = $requestComments->fetch(PDO::FETCH_ASSOC)) {
                $comment = $this->createEntityFromTableData($commentData, 'Comment');
                if ($comment->isApproved()) {
                    $comments[] = $comment;
                }
            }

        } else {
            while ($commentData = $requestComments->fetch(PDO::FETCH_ASSOC)) {
                $comment = $this->createEntityFromTableData($commentData, 'Comment');
                $comments[] = $comment;
            }
        }

        return $comments;
    }

    // Private

    /**
     * Fill parents and children properties of comments
     *
     * @param array $comments
     */
    private static function fillParentsAndChildren(array $comments)
    {
        foreach ($comments as $comment) {
            if ($comment->getParentId()) {
                $parent = $comments[$comment->getParentId()];
                $comment->setParent($parent);
                $parent->addAChild($comment);
            }
        }
    }
}
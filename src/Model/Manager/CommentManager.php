<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 22/01/2019
 * Time: 14:41
 */

namespace Model\Manager;

use Application\Exception\HttpException;
use Model\Entity\Comment;
use \Exception;
use PDO;

class CommentManager extends Manager
{
    public static $commentsPerPage = 5;

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
     * @param int|null $numberOfLines
     * @param int|null $start
     * @return array
     * @throws \Application\Exception\HttpException
     */
    public function getAll(?int $numberOfLines = null, ?int $start = null): array
    {
        $comments = parent::getAll($numberOfLines, $start);

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
     * @param int|null $numberOfLines
     * @param int|null $page
     * @param bool $filterApproved
     * @return array
     * @throws HttpException
     */
    public function getFromPost(int $postId, ?int $numberOfLines = null, ?int $page = 1, bool $filterApproved = true): array
    {
        $comments = [];
        $query = 'SELECT * FROM bl_comment
            WHERE com_post_id_fk = :postId';

        if ($filterApproved) {
            $query .= " AND com_approved = 1";
        }

        if ($page < 1) {
            $page = 1;
        }

        if ($numberOfLines) {
            $query .= " AND com_parent_id_fk IS NULL";
            self::addLimitToQuery($query, $numberOfLines, ($page - 1) * $numberOfLines);
        }

        $requestComments = $this->query($query, ['postId' => $postId]);

        while ($commentData = $requestComments->fetch(PDO::FETCH_ASSOC)) {
            $comment = $this->buildComment($commentData);
            $comment->setChildren($this->getChildren($comment->getId()));
            $comments[$comment->getId()] = $comment;
        }

        return $comments;
    }

    /**
     * Get the name of the comment's author
     *
     * @param int $memberId
     * @return string|null
     * @throws \Application\Exception\HttpException
     */
    public function getCommentMember(?int $memberId): ?string
    {
        if ($memberId) {
            $member = null;

            $query = 'SELECT m_name FROM bl_member
            WHERE m_id = :id';

            $requestAuthor = $this->query($query, ['id' => $memberId]);

            $member = $requestAuthor->fetch(PDO::FETCH_ASSOC);

            return $member['m_name'];
        }
        return "Un ancien membre qui n'est plus des nôtres";
    }

    /**
     * Get the title of the post associated to the comment
     *
     * @param int $postId
     * @return mixed
     * @throws \Application\Exception\HttpException
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
     * @throws \Application\Exception\HttpException
     */
    public function getCommentsOfAMember(int $memberId, bool $filterApproved = true, ?int $numberOfComments = null, ?int $start = null)
    {
        $comments = [];

        $query = 'SELECT ' . implode(', ', $this->fields) . ' FROM bl_comment WHERE com_author_id_fk = :memberId';

        if ($filterApproved) {
            $query .= " AND com_approved = 1";
        }

        if ($numberOfComments) {
            self::addLimitToQuery($query, $numberOfComments, $start);
        }
        $requestComments = $this->query($query, ['memberId' => $memberId]);

        while ($commentData = $requestComments->fetch(PDO::FETCH_ASSOC)) {
            $comment = $this->createEntityFromTableData($commentData);
            $comments[] = $comment;
        }

        return $comments;
    }

    /**
     * Count the number of comments of a post
     *
     * @param int $postId
     * @param int|null $memberId
     * @param bool $countChildren
     * @param bool $filterApproved
     * @return mixed
     * @throws HttpException
     */
    public function countComments(?int $postId = null, ?int $memberId = null, bool $countChildren = true, bool $filterApproved = true)
    {
        if ($postId) {
            $query = "SELECT COUNT(com_id) FROM bl_comment WHERE com_post_id_fk = :id";
            $id = $postId;

        } elseif ($memberId) {
            $query = "SELECT COUNT(com_id) FROM bl_comment WHERE com_author_id_fk = :id";
            $id = $memberId;

        } else {
            throw new HttpException("Lacking post id or member id", 500);
        }

        if (!$countChildren) {
            $query .= " AND com_parent_id_fk IS NULL";
        }
        if ($filterApproved) {
            $query .= " AND com_approved = 1";
        }
        $requestCount = $this->query($query, ["id" => $id]);

        return $requestCount->fetch(PDO::FETCH_NUM)[0];
    }

    // Private

    /**
     * Get children of a comment
     *
     * @param int $commentId
     * @return array
     * @throws \Application\Exception\HttpException
     */
    private function getChildren(int $commentId)
    {
        $children = [];

        $query = "SELECT * FROM bl_comment
            WHERE com_parent_id_fk = :commentId";
        $requestChildren = $this->query($query, ["commentId" => $commentId]);

        while ($commentData = $requestChildren->fetch(PDO::FETCH_ASSOC)) {
            $comment = $this->buildComment($commentData);
            if ($this->countChildren($comment->getId()) > 0) {
                $commentChildren = $this->getChildren($comment->getId());
                foreach ($commentChildren as $commentChild) {
                    $commentChild->setParent($comment);
                }
                $comment->setChildren($commentChildren);
            }
            $children[$comment->getId()] = $comment;
        }
        return $children;
    }

    /**
     * Count the number of children of a comment
     *
     * @param int $commentId
     * @return mixed
     * @throws \Application\Exception\HttpException
     */
    private function countChildren(int $commentId)
    {
        $query = "SELECT COUNT(com_id) FROM bl_comment
            WHERE com_parent_id_fk = :commentId";
        $requestCount = $this->query($query, ["commentId" => $commentId]);

        return $requestCount->fetch(PDO::FETCH_NUM)[0];
    }

    /**
     * @param array $commentData
     * @return Comment
     * @throws \Application\Exception\HttpException
     */
    private function buildComment(array $commentData): Comment
    {
        $comment = $this->createEntityFromTableData($commentData);
        $comment->setAuthor($this->getCommentMember($comment->getAuthorId()));
        $comment->setPostTitle($this->getPostTitle($comment->getPostId()));
        if ($comment->getLastEditorId()) {
            $comment->setLastEditor = $this->getCommentMember($comment->getLastEditorId());
        }
        return $comment;
    }
}

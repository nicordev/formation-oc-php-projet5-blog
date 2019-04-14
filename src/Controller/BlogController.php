<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 09:03
 */

namespace Controller;


use Application\Exception\HttpException;
use Helper\BlogHelper;
use Exception;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\MemberManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;
use Twig_Environment;

class BlogController extends Controller
{
    protected $postManager;
    protected $tagManager;
    protected $categoryManager;
    protected $commentManager;
    protected $memberManager;
    protected $postsPerPage = 5;
    protected $commentsPerPage = 3;

    public const VIEW_BLOG = 'blog/blog.twig';
    public const VIEW_BLOG_TAG = 'blog/tagPage.twig';
    public const VIEW_BLOG_POST = 'blog/blogPost.twig';

    public const KEY_MESSAGE = "message";
    public const KEY_POST = "post";
    public const KEY_POSTS = "posts";
    public const KEY_POST_ID = "post-id";
    public const KEY_COMMENT_ID = "comment-id";
    public const KEY_COMMENTS = "comments";
    public const KEY_COMMENTS_PAGE = "commentsPage";
    public const KEY_COMMENTS_PAGES_COUNT = "commentsPagesCount";
    public const KEY_COMMENTS_COUNT = "commentsCount";
    public const KEY_CATEGORY = "category";
    public const KEY_CATEGORIES = "categories";
    public const KEY_PAGES_COUNT = "pagesCount";
    public const KEY_CURRENT_PAGE = "currentPage";
    public const KEY_PREVIOUS_PAGE = "previousPage";
    public const KEY_NEXT_PAGE = "nextPage";
    public const KEY_TAG = "tag";
    public const KEY_TAGS = 'tags';

    /**
     * BlogController constructor.
     *
     * @param PostManager $postManager
     * @param TagManager $tagManager
     * @param CategoryManager $categoryManager
     * @param CommentManager $commentManager
     * @param MemberManager $memberManager
     * @param Twig_Environment $twig
     */
    public function __construct(
        PostManager $postManager,
        TagManager $tagManager,
        CategoryManager $categoryManager,
        CommentManager $commentManager,
        MemberManager $memberManager,
        Twig_Environment $twig
    ) {
        parent::__construct($twig);
        $this->postManager = $postManager;
        $this->tagManager = $tagManager;
        $this->categoryManager = $categoryManager;
        $this->commentManager = $commentManager;
        $this->memberManager = $memberManager;
    }

    // Views

    /**
     * Show all posts of a given category
     *
     * @param int $categoryId
     * @param int|null $page
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    public function showPostsOfACategory(int $categoryId, ?int $page = null)
    {
        $numberOfPosts = $this->postManager->countPostsOfACategory($categoryId);
        $numberOfPages = ceil($numberOfPosts / $this->postsPerPage);

        if ($page >= $numberOfPages) {
            $page = $numberOfPages;
        } elseif ($page <= 0) {
            $page = 1;
        }

        if ($page > 1) {
            $start = ($page - 1) * $this->postsPerPage;
            $posts = $this->postManager->getPostsOfACategory($categoryId, $this->postsPerPage, $start, false);
            if ($page < $numberOfPages) {
                $nextPage = $page + 1;
            }
            $previousPage = $page - 1;
        } else {
            $posts = $this->postManager->getPostsOfACategory($categoryId, $this->postsPerPage, null, false);
            if ($numberOfPages > 1) {
                $nextPage = 2;
            }
        }

        $category = $this->categoryManager->get($categoryId);

        foreach ($posts as $post) {
            BlogHelper::prepareAPost($post);
        }

        $this->render(self::VIEW_BLOG, [
            self::KEY_POSTS => $posts,
            self::KEY_CATEGORY => $category,
            self::KEY_NEXT_PAGE => $nextPage ?? null,
            self::KEY_PREVIOUS_PAGE => $previousPage ?? null,
            self::KEY_CURRENT_PAGE => $page,
            self::KEY_PAGES_COUNT => $numberOfPages
        ]);
    }

    /**
     * Show all the posts associated to a tag
     *
     * @param int $tagId
     * @param int|null $page
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    public function showPostsOfATag(int $tagId, ?int $page = null)
    {
        $numberOfPosts = $this->postManager->countPostsOfATag($tagId);
        $numberOfPages = ceil($numberOfPosts / $this->postsPerPage);

        if ($page >= $numberOfPages) {
            $page = $numberOfPages;
        } elseif ($page <= 0) {
            $page = 1;
        }

        if ($page > 1) {
            $start = ($page - 1) * $this->postsPerPage;
            $posts = $this->postManager->getPostsOfATag($tagId, $this->postsPerPage, $start, false);
            if ($page < $numberOfPages) {
                $nextPage = $page + 1;
            }
            $previousPage = $page - 1;
        } else {
            $posts = $this->postManager->getPostsOfATag($tagId, $this->postsPerPage, null, false);
            if ($numberOfPages > 1) {
                $nextPage = 2;
            }
        }

        foreach ($posts as $post) {
            BlogHelper::prepareAPost($post);
        }
        $tag = $this->tagManager->get($tagId);

        $this->render(self::VIEW_BLOG_TAG, [
            self::KEY_POSTS => $posts,
            self::KEY_TAG => $tag,
            self::KEY_NEXT_PAGE => $nextPage ?? null,
            self::KEY_PREVIOUS_PAGE => $previousPage ?? null,
            self::KEY_CURRENT_PAGE => $page,
            self::KEY_PAGES_COUNT => $numberOfPages
        ]);
    }

    /**
     * Show an entire blog post
     *
     * @param int $postId
     * @param string|null $message
     * @param int|null $commentsPage
     * @return void
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws HttpException
     */
    public function showASinglePost(int $postId, ?string $message = null, ?int $commentsPage = 1)
    {
        $post = $this->postManager->get($postId);
        BlogHelper::prepareAPost($post);

        $commentsCount = $this->commentManager->countComments($postId, null, true);
        $rootCommentsCount = $this->commentManager->countComments($postId, null, false);
        $commentsPagesCount = ceil($rootCommentsCount / $this->commentsPerPage);

        if ($commentsPage < 1) {
            $commentsPage = 1;
        } elseif ($commentsPage > $commentsPagesCount) {
            $commentsPage = $commentsPagesCount;
        }

        $comments = $this->commentManager->getFromPost($postId, $this->commentsPerPage, $commentsPage);
        foreach ($comments as $comment) {
            BlogHelper::convertDatesOfComment($comment);
        }

        $this->render(self::VIEW_BLOG_POST, [
            self::KEY_POST => $post,
            self::KEY_COMMENTS => $comments,
            self::KEY_COMMENTS_PAGE => $commentsPage,
            self::KEY_COMMENTS_PAGES_COUNT => $commentsPagesCount,
            self::KEY_COMMENTS_COUNT => $commentsCount,
            self::KEY_MESSAGE => $message
        ]);
    }
}

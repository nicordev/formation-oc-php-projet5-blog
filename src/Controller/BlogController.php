<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 09:03
 */

namespace Controller;


use Application\Exception\HttpException;
use Application\Exception\PageNotFoundException;
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

    const VIEW_BLOG = 'blog/blog.twig';
    const VIEW_BLOG_TAG = 'blog/tagPage.twig';
    const VIEW_BLOG_POST = 'blog/blogPost.twig';

    const KEY_MESSAGE = "message";
    const KEY_POST = "post";
    const KEY_POSTS = "posts";
    const KEY_POST_ID = "post-id";
    const KEY_COMMENT_ID = "comment-id";
    const KEY_COMMENTS = "comments";
    const KEY_CATEGORY = "category";
    const KEY_CATEGORIES = "categories";

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
            'nextPage' => $nextPage ?? null,
            'previousPage' => $previousPage ?? null,
            'currentPage' => $page,
            'pagesCount' => $numberOfPages
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
            'tag' => $tag,
            'nextPage' => isset($nextPage) ? $nextPage : null,
            'previousPage' => isset($previousPage) ? $previousPage : null
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
            "commentsPage" => $commentsPage,
            "commentsPagesCount" => $commentsPagesCount,
            "commentsCount" => $commentsCount,
            self::KEY_MESSAGE => $message
        ]);
    }
}

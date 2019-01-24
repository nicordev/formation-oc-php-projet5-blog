<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 09:03
 */

namespace Controller;


use Application\Exception\BlogException;
use Exception;
use Model\Entity\Post;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;
use Twig_Environment;

class BlogController
{
    protected $postManager;
    protected $tagManager;
    protected $categoryManager;
    protected $commentManager;
    protected $viewFolderPath = '';
    protected $twig;

    /**
     * BlogController constructor.
     *
     * @param Twig_Environment $twig
     * @param PostManager $postManager
     * @param TagManager $tagManager
     * @param CategoryManager $categoryManager
     * @param CommentManager $commentManager
     */
    public function __construct(
                                PostManager $postManager,
                                TagManager $tagManager,
                                CategoryManager $categoryManager,
                                CommentManager $commentManager,
                                Twig_Environment $twig
    )
    {
        $this->postManager = $postManager;
        $this->tagManager = $tagManager;
        $this->categoryManager = $categoryManager;
        $this->commentManager = $commentManager;
        $this->twig = $twig;
    }

    // Views

    /**
     * Show all posts of the blog
     */
    public function showAllPosts()
    {
        echo $this->twig->render('blog.twig', ['posts' => $this->postManager->getAll()]);
    }

    /**
     * Show an entire blog post
     *
     * @param $postId
     * @return void
     * @throws Exception
     */
    public function showASinglePost(int $postId)
    {
        try {
            $post = $this->postManager->get($postId);
            $nextPostId = $this->getNextPostId($postId);
            $previousPostId = $this->getPreviousPostId($postId);

        } catch (BlogException $e) {
            $this->pageNotFound404();
        }

        require $this->viewFolderPath . '/blogPost.php';
    }

    /**
     * Show the panel do manage blog posts
     */
    public function showAdminPanel(string $message = '')
    {
        $posts = $this->postManager->getAll();

        require $this->viewFolderPath . '/blogAdmin.php';
    }

    /**
     * Show the post editor
     *
     * @param int $postToEditId
     * @param string $message
     * @throws BlogException
     */
    public function showPostEditor(int $postToEditId = Post::NO_ID, string $message = '')
    {
        $postToEdit = null;

        if ($postToEditId !== Post::NO_ID) {
            $postToEdit = $this->postManager->get($postToEditId);
        }

        require $this->viewFolderPath . '/postEditor.php';
    }

    /**
     * Show a page when the visitor is lost...
     */
    public function pageNotFound404()
    {
        require $this->viewFolderPath . '/pageNotFound.php';
        exit();
    }

    // Actions

    /**
     * Add a new post from $_POST
     *
     * @throws BlogException
     */
    public function addPost()
    {
        $newPost = self::buildPostFromForm();

        if ($newPost !== null) {
            $this->postManager->add($newPost);
            // Come back to the admin panel
            $this->showAdminPanel("Un article a été publié.");

        } else {
            // Try again...
            $this->showPostEditor(Post::NO_ID, "Erreur : le titre, l'extrait et le contenu de l'article ne doivent pas être vides.");
        }
    }

    /**
     * Edit an existing post from $_POST
     *
     * @throws BlogException
     */
    public function editPost()
    {
        $modifiedPost = self::buildPostFromForm();

        if ($modifiedPost !== null) {
            $this->postManager->edit($modifiedPost);
            // Come back to the admin panel
            $this->showAdminPanel("Un article a été modifié.");
        } else {
            // Try again...
            $this->showPostEditor(htmlspecialchars($_POST['edit-post']), "Erreur : le titre, l'extrait et le contenu de l'article ne doivent pas être vides.");
        }
    }

    /**
     * Delete a post
     *
     * @throws BlogException
     */
    public function deletePost()
    {
        $postId = (int) $_POST['delete-post'];
        $this->postManager->delete($postId);
        // Come back to the admin panel
        $this->showAdminPanel("Un article a été supprimé.");
    }

    // Private

    /**
     * Create a Post from a form (thanks to $_POST)
     * Work for addPost and editPost
     *
     * @return Post|null
     */
    private static function buildPostFromForm(): ?Post
    {
        $post = new Post();

        if (
            isset($_POST['post-title']) && !empty($_POST['post-title']) &&
            isset($_POST['post-excerpt']) && !empty($_POST['post-excerpt']) &&
            isset($_POST['post-content']) && !empty($_POST['post-content']) &&
            isset($_POST['post-author-id'])
        ) {
            // Common
            $post->setTitle(htmlspecialchars($_POST['post-title']));
            $post->setExcerpt(htmlspecialchars($_POST['post-excerpt']));
            $post->setContent(htmlspecialchars($_POST['post-content']));
            $post->setAuthorId(htmlspecialchars($_POST['post-author-id']));

            // Edit a post
            if (isset($_POST['edit-post'])) {
                $post->setId(htmlspecialchars($_POST['edit-post']));
            }
            if (isset($_POST['post-editor-id'])) {
                $post->setLastEditorId(htmlspecialchars($_POST['post-editor-id']));
            }

            return $post;

        } else {
            return null;
        }
    }

    /**
     * Get the next Post id or false if it's the last post
     *
     * @param int $postId
     * @return bool
     */
    private function getNextPostId(int $postId)
    {
        $ids = $this->postManager->getAllIds();

        for ($i = 0, $size = count($ids); $i < $size; $i++) {
            if ($postId < $ids[$i]) {
                return $ids[$i];
            }
        }

        return false;
    }

    /**
     * Get the previous Post id or false if it's the first post
     *
     * @param int $postId
     * @return bool
     */
    private function getPreviousPostId(int $postId)
    {
        $ids = $this->postManager->getAllIds();

        for ($i = count($ids) - 1; $i >= 0; $i--) {
            if ($postId > $ids[$i]) {
                return $ids[$i];
            }
        }

        return false;
    }
}
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
use Model\Entity\Tag;
use Model\Manager\CategoryManager;
use Model\Manager\CommentManager;
use Model\Manager\PostManager;
use Model\Manager\TagManager;
use Twig_Environment;

class BlogController extends Controller
{
    protected $postManager;
    protected $tagManager;
    protected $categoryManager;
    protected $commentManager;
    protected $twig;

    const VIEW_BLOG = 'blog/blog.twig';
    const VIEW_BLOG_POST = 'blog/blogPost.twig';
    const VIEW_BLOG_ADMIN = 'blog/blogAdmin.twig';
    const VIEW_POST_EDITOR = 'blog/postEditor.twig';

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
        parent::__construct($twig);
        $this->postManager = $postManager;
        $this->tagManager = $tagManager;
        $this->categoryManager = $categoryManager;
        $this->commentManager = $commentManager;
    }

    // Views

    /**
     * Show all posts of the blog
     */
    public function showAllPosts()
    {
        $posts = $this->postManager->getAll();

        self::render(self::VIEW_BLOG, ['posts' => $posts]);
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
            $this->pageNotFound404(); // TODO throw an Exception instead
        }

        self::render(self::VIEW_BLOG_POST, [
            'post' => $post,
            'nextPostId' => $nextPostId,
            'previousPostId' => $previousPostId
        ]);
    }

    /**
     * Show the panel do manage blog posts
     *
     * @param string $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showAdminPanel(string $message = '')
    {
        $posts = $this->postManager->getAll();

        self::render(self::VIEW_BLOG_ADMIN, [
            'posts' => $posts,
            'message' => $message
        ]);
    }

    /**
     * Show the post editor
     *
     * @param int $postToEditId
     * @param string $message
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPostEditor(int $postToEditId = Post::NO_ID, string $message = '')
    {
        $postToEdit = null;
        $availableTags = $this->tagManager->getAll();
        $availableTagNames = [];
        $selectedTagNames = [];

        foreach ($availableTags as $availableTag) {
            $availableTagNames[] = $availableTag->getName();
        }

        if ($postToEditId !== Post::NO_ID) {
            $postToEdit = $this->postManager->get($postToEditId);

            foreach ($postToEdit->getTags() as $tag) {
                $selectedTagNames[] = $tag->getName();
            }
        }

        self::render(self::VIEW_POST_EDITOR, [
            'postToEdit' => $postToEdit,
            'postToEditId' => $postToEditId,
            'message' => $message,
            'availableTags' => $availableTagNames,
            'selectedTags' => $selectedTagNames
        ]);
    }

    // Actions

    /**
     * Add a new post from $_POST and add associated tags
     *
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addPost()
    {
        $newPost = self::buildPostFromForm();
        $tags = self::getTagsFromForm();

        if ($newPost !== null) {

            if ($tags !== null) {
                foreach ($tags as $tag) {
                    // Add new tag
                    if ($this->tagManager->isNewTag($tag)) {
                        $this->tagManager->add($tag);
                    }
                    // Set tag id
                    $id = $this->tagManager->getId($tag->getName());
                    $tag->setId($id);
                }
                // Associate tags and post
                $newPost->setTags($tags);
            }

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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
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
     * Return Tag entities
     *
     * @return array
     */
    private static function getTagsFromForm(): array
    {
        $tags = null;

        if (isset($_POST['tags'])) {
            foreach ($_POST['tags'] as $tag) {
                $tags[] = new Tag(['name' => $tag]);
            }
        }

        return $tags;
    }

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
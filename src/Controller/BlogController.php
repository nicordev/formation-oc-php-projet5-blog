<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 09:03
 */

namespace Controller;


use Application\Exception\AccessException;
use Application\Exception\AppException;
use Application\Exception\BlogException;
use Application\Exception\PageNotFoundException;
use Application\FileHandler\ImageHandler;
use Exception;
use Michelf\Markdown;
use Model\Entity\Category;
use Model\Entity\Comment;
use Model\Entity\Entity;
use Model\Entity\Post;
use Model\Entity\Tag;
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
    protected $postsByPage = 10;

    const VIEW_BLOG = 'blog/blog.twig';
    const VIEW_BLOG_TAG = 'blog/tagPage.twig';
    const VIEW_BLOG_POST = 'blog/blogPost.twig';
    const VIEW_BLOG_ADMIN = 'admin/blogAdmin.twig';
    const VIEW_POST_EDITOR = 'admin/postEditor.twig';
    const VIEW_CATEGORY_EDITOR = 'admin/categoryEditor.twig';
    const VIEW_COMMENT_EDITOR = 'admin/commentEditor.twig';
    const VIEW_MEDIA_LIBRARY = 'admin/mediaLibrary.twig';
    const VIEW_IMAGE_EDITOR = 'admin/imageEditor.twig';

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
    )
    {
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
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    public function showPostsOfACategory(int $categoryId, ?int $page = null)
    {
        $numberOfPosts = $this->postManager->countPostsOfACategory($categoryId);
        $numberOfPages = ceil($numberOfPosts / $this->postsByPage);

        if ($page >= $numberOfPages) {
            $page = $numberOfPages;
        } elseif ($page <= 0) {
            $page = 1;
        }

        if ($page > 1) {
            $start = ($page - 1) * $this->postsByPage;
            $posts = $this->postManager->getPostsOfACategory($categoryId, $this->postsByPage, $start, false);
            if ($page < $numberOfPages) {
                $nextPage = $page + 1;
            }
            $previousPage = $page - 1;
        } else {
            $posts = $this->postManager->getPostsOfACategory($categoryId, $this->postsByPage, null, false);
            if ($numberOfPages > 1) {
                $nextPage = 2;
            }
        }

        $category = $this->categoryManager->get($categoryId);

        foreach ($posts as $post) {
            self::prepareAPost($post);
        }

        $this->render(self::VIEW_BLOG, [
            'posts' => $posts,
            'category' => $category,
            'nextPage' => isset($nextPage) ? $nextPage : null,
            'previousPage' => isset($previousPage) ? $previousPage : null
        ]);
    }

    /**
     * Show all the posts associated to a tag
     *
     * @param int $tagId
     * @param int|null $page
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    public function showPostsOfATag(int $tagId, ?int $page = null)
    {
        $numberOfPosts = $this->postManager->countPostsOfATag($tagId);
        $numberOfPages = ceil($numberOfPosts / $this->postsByPage);

        if ($page >= $numberOfPages) {
            $page = $numberOfPages;
        } elseif ($page <= 0) {
            $page = 1;
        }

        if ($page > 1) {
            $start = ($page - 1) * $this->postsByPage;
            $posts = $this->postManager->getPostsOfATag($tagId, $this->postsByPage, $start, false);
            if ($page < $numberOfPages) {
                $nextPage = $page + 1;
            }
            $previousPage = $page - 1;
        } else {
            $posts = $this->postManager->getPostsOfATag($tagId, $this->postsByPage, null, false);
            if ($numberOfPages > 1) {
                $nextPage = 2;
            }
        }

        foreach ($posts as $post) {
            self::prepareAPost($post);
        }
        $tag = $this->tagManager->get($tagId);

        $this->render(self::VIEW_BLOG_TAG, [
            'posts' => $posts,
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
     * @return void
     * @throws PageNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    public function showASinglePost(int $postId, ?string $message = null)
    {
        try {
            $post = $this->postManager->get($postId);
            self::prepareAPost($post);

            $comments = $this->commentManager->getFromPost($postId);
            foreach ($comments as $comment) {
                self::convertDatesOfComment($comment);
            }

        } catch (BlogException $e) {
            throw new PageNotFoundException('This post do not exists.');
        }

        $this->render(self::VIEW_BLOG_POST, [
            'post' => $post,
            'comments' => $comments,
            'message' => $message
        ]);
    }

    /**
     * Show the panel do manage blog posts
     *
     * @param string $message
     * @param array $yesNoForm
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AccessException
     */
    public function showAdminPanel(string $message = '', array $yesNoForm = [])
    {
        $posts = $this->postManager->getAll();
        $tags = $this->tagManager->getAll();
        $categories = $this->categoryManager->getAll();
        $comments = $this->commentManager->getAll();

        if (MemberController::memberConnected()) {
            if (in_array('admin', $_SESSION['connected-member']->getRoles())) {
                $members = $this->memberManager->getAll();
            }
        } else {
            throw new AccessException('No connected member found.');
        }

        $this->render(self::VIEW_BLOG_ADMIN, [
            'posts' => $posts,
            'message' => $message,
            'yesNoForm' => $yesNoForm,
            'tags' => $tags,
            'categories' => $categories,
            'comments' => $comments,
            'members' => $members ?? null
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
    public function showPostEditor(?int $postToEditId = null, string $message = '')
    {
        $postToEdit = null;
        $categories = $this->categoryManager->getAll();
        $availableTags = $this->tagManager->getAll();
        $availableTagNames = self::getTagNames($availableTags);
        $selectedTagNames = [];
        $markdown = false;

        if ($postToEditId !== null) {
            $postToEdit = $this->postManager->get($postToEditId);
            $selectedTagNames = self::getTagNames($postToEdit->getTags());
            $markdown = $postToEdit->isMarkdown();
        }

        $this->render(self::VIEW_POST_EDITOR, [
            'postToEdit' => $postToEdit,
            'postToEditId' => $postToEditId,
            'categories' => $categories,
            'message' => $message,
            'availableTags' => $availableTagNames,
            'selectedTags' => $selectedTagNames,
            'markdown' => $markdown
        ]);
    }

    /**
     * Show the category editor
     *
     * @param int|null $categoryToEditId
     * @param string $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws BlogException
     */
    public function showCategoryEditor(?int $categoryToEditId = null, string $message = '')
    {
        $categoryToEdit = null;

        if ($categoryToEditId !== null) {
            $categoryToEdit = $this->categoryManager->get($categoryToEditId);
        }

        $this->render(self::VIEW_CATEGORY_EDITOR, [
            'categoryToEdit' => $categoryToEdit,
            'categoryToEditId' => $categoryToEditId,
            'message' => $message
        ]);
    }

    /**
     * Show the comment editor page
     *
     * @param int|null $commentToEditId
     * @param string $message
     * @throws PageNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showCommentEditor(?int $commentToEditId = null, string $message = '')
    {
        if (!$commentToEditId) {
            if (isset($_POST['comment-id']) && !empty($_POST['comment-id'])) {
                $commentToEditId = (int) $_POST['comment-id'];
            } else {
                throw new PageNotFoundException('It lacks the comment to edit id.');
            }
        }

        $comment = $this->commentManager->get($commentToEditId);

        $this->render(self::VIEW_COMMENT_EDITOR, [
            'commentToEdit' => $comment
        ]);
    }

    /**
     * Show the media library
     *
     * @param string|null $callingPage
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Application\Exception\ImageException
     */
    public function showMediaLibrary()
    {
        $images = ImageHandler::getAllPath();

        $this->render(self::VIEW_MEDIA_LIBRARY, [
            'images' => $images
        ]);
    }

    public function showImageEditor(string $image = null)
    {
        $this->render(self::VIEW_IMAGE_EDITOR, [
            'image' => $image
        ]);
    }

    // Actions

    /**
     * Add a new post from $_POST and add associated tags
     *
     * @throws BlogException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addPost()
    {
        $newPost = self::buildPostFromForm();
        $newPost->setCreationDate(date(self::MYSQL_DATE_FORMAT));
        $newPost->setLastModificationDate(date(self::MYSQL_DATE_FORMAT));
        $newPost->setLastEditorId($newPost->getAuthorId());
        $messages = [];

        if ($newPost !== null) {
            $this->handleAPost($newPost, true);
        } else {
            // Try again...
            $this->showPostEditor(null, "Erreur : le titre, l'extrait et le contenu de l'article ne doivent pas être vides.");
        }
    }

    /**
     * Edit an existing post from $_POST
     *
     * @throws BlogException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editPost()
    {
        $modifiedPost = self::buildPostFromForm();
        $tags = $modifiedPost->getTags();
        $message = '';

        if ($modifiedPost !== null) {
            $this->handleAPost($modifiedPost, false);
        } else {
            // Try again...
            $this->showPostEditor((int) $_POST['edit-post'], "Erreur : le titre, l'extrait et le contenu de l'article ne doivent pas être vides.");
        }
    }

    /**
     * Delete a post from $_POST
     *
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AccessException
     */
    public function deletePost()
    {
        $postId = (int) $_POST['delete-post'];
        $this->postManager->delete($postId);
        // Come back to the admin panel
        $this->showAdminPanel("Un article a été supprimé.");
    }

    /**
     * Update the list of tags in the database
     *
     * @param array $tagIds
     * @param array $tagNames
     * @param string|null $action
     * @return bool
     * @throws AppException
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AccessException
     */
    public function updateTagList(?array $tagIds, ?array $tagNames, ?string $action = null)
    {
        if ($tagIds === null || $tagNames === null) {
            if ($action === 'delete-all') {
                $this->tagManager->deleteAll(); // TODO: add a confirmation before delete all
                // Head back to the admin panel
                $this->showAdminPanel('Toutes les etiquettes ont été supprimées.');
            } else {
                // Head back to the admin panel
                $yesNoForm = [
                    'yesAction' => '/admin/update-tags?action=delete-all',
                    'noAction' => '/admin'
                ];
                $this->showAdminPanel('Vous êtes sur le point de supprimer toutes les étiquettes. Continuer ?', $yesNoForm);
            }
            return false;
        }

        $oldTags = $this->tagManager->getAll();

        // Add new tags
        $this->addNewTagsFromTagList($tagIds, $tagNames);

        // Update of delete tags
        $tagIds = array_map('intval', $tagIds); // Convert string to int
        foreach ($oldTags as $oldTag) {
            // Delete or update tag ?
            if (self::isEntityToDelete($oldTag, $tagIds)) {
                $this->tagManager->delete($oldTag->getId());
            } else {
                $this->updateTag($oldTag, $tagIds, $tagNames);
            }
        }
        // Head back to the admin panel
        $this->showAdminPanel('La liste des étiquettes a été mise à jour.');
    }

    /**
     * Add a new category from $_POST
     */
    public function addCategory()
    {
        $newCategory = self::buildCategoryFromForm();

        if ($newCategory !== null) {
            $this->categoryManager->add($newCategory);

            // Come back to the admin panel
            $this->showAdminPanel("Une catégorie a été créée.");

        } else {
            // Try again...
            $this->showCategoryEditor(null, "Erreur : la catégorie doit avoir un nom.");
        }
    }

    /**
     * Edit a category in the database
     *
     * @return void
     * @throws AccessException
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editCategory()
    {
        $modifiedCategory = self::buildCategoryFromForm();

        if ($modifiedCategory !== null) {
            $this->categoryManager->edit($modifiedCategory);

            // Come back to the admin panel
            $this->showAdminPanel("Une catégorie a été modifiée.");

        } else {
            // Try again...
            $this->showCategoryEditor((int) $_POST['edit-category'], "Erreur : la catégorie doit avoir un nom.");
        }
    }

    /**
     * Delete a category from $_POST
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws BlogException
     * @throws AccessException
     */
    public function deleteCategory()
    {
        $categoryId = (int) $_POST['delete-category'];
        $this->categoryManager->delete($categoryId);
        // Come back to the admin panel
        $this->showAdminPanel("Une catégorie a été supprimée.");
    }

    /**
     * Add a comment to the database
     *
     * @throws PageNotFoundException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addComment()
    {
        $comment = $this->buildCommentFromForm();

        if (empty($comment->getContent())) {
            $this->showASinglePost($comment->getPostId(), 'Votre commentaire ne doit pas être vide.');

        } else {
            $comment->setCreationDate(date(self::MYSQL_DATE_FORMAT));
            $this->commentManager->add($comment);
            $this->showASinglePost($comment->getPostId(), 'Votre commentaire a été envoyé. Il sera vérifié dans les prochains jours.');
        }
    }

    /**
     * Edit a comment in the database
     */
    public function editComment()
    {
        $modifiedComment = $this->buildCommentFromForm();
        $modifiedComment->setLastModificationDate(date(self::MYSQL_DATE_FORMAT));

        $this->commentManager->edit($modifiedComment);

        if ($modifiedComment->isApproved()) {
            // Come back to the admin panel
            $this->showAdminPanel("Un commentaire a été approuvé.");
        } else {
            // Come back to the admin panel
            $this->showAdminPanel("Un commentaire non approuvé a été modifié.");
        }
    }

    /**
     * Delete a comment in the database
     *
     * @throws AccessException
     * @throws BlogException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function deleteComment()
    {
        $commentId = (int) $_POST['delete-comment'];
        $this->commentManager->delete($commentId);

        // Come back to the admin panel
        $this->showAdminPanel("Un commentaire a été supprimé.");
    }

    /**
     * Add an image in the library
     * @throws \Application\Exception\FileException
     * @throws \Application\Exception\ImageException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addImage()
    {
        $path = ImageHandler::uploadImage('new-image', '', 'blog_', '_post');
        ImageHandler::editImage($path, [
            'width' => 500,
            'height' => 150,
            'x' => 20,
            'y' => 30
        ]);

        $this->showMediaLibrary();
    }

    /**
     * Prepare a post before showing it (convert dates and markdown contents)
     *
     * @param Post $post
     * @throws Exception
     */
    public static function prepareAPost(Post $post)
    {
        self::convertDatesOfPost($post);
        if ($post->isMarkdown() && !empty($post->getContent())) {
            $post->setContent(self::convertMarkdown($post->getContent()));
        }
    }

    /**
     * Change the date format use in a post
     *
     * @param Post $post
     * @throws Exception
     */
    public static function convertDatesOfPost(Post $post)
    {
        $post->setCreationDate(self::formatDate($post->getCreationDate()));

        if ($post->getLastModificationDate() !== null) {
            $post->setLastModificationDate(self::formatDate($post->getLastModificationDate()));
        }
    }

    /**
     * Change the date format use in a comment
     *
     * @param Comment $comment
     * @throws Exception
     */
    public static function convertDatesOfComment(Comment $comment)
    {
        $comment->setCreationDate(self::formatDate($comment->getCreationDate()));

        if ($comment->getLastModificationDate() !== null) {
            $comment->setLastModificationDate(self::formatDate($comment->getLastModificationDate()));
        }
    }

    // Private

    /**
     * Add new tags in the database and get theirs ids
     *
     * @param array|null $tags
     * @return array|null
     * @throws Exception
     */
    private function addNewTags(array $tags)
    {
        foreach ($tags as $tag) {
            // Add new tag
            if ($this->tagManager->isNewTag($tag)) {
                $this->tagManager->add($tag);
            }
            // Set tag id
            $id = $this->tagManager->getId($tag->getName());
            $tag->setId($id);
        }

        return $tags;
    }

    /**
     * Extract names from an array of Tag
     *
     * @param array $tags
     * @return mixed
     */
    private static function getTagNames(array $tags)
    {
        $tagNames = [];
        foreach ($tags as $tag) {
            $tagNames[] = $tag->getName();
        }
        return $tagNames;
    }

    /**
     * Add new tags to the database if tag id === 'new'
     *
     * @param array $tagIds
     * @param array $tagNames
     * @return int
     * @throws AppException
     */
    private function addNewTagsFromTagList(array $tagIds, array $tagNames)
    {
        $numberOfNewTags = 0;

        for ($i = count($tagIds) - 1; $i >= 0; $i--) {
            if ($tagIds[$i] === 'new') {
                try {
                    $this->tagManager->add(new Tag(['name' => $tagNames[$i]]));
                } catch (Exception $e) {
                    throw new AppException('Impossible to add the tag ' . $tagNames[$i], 0, $e);
                }
                $numberOfNewTags++;

            } else {
                break;
            }
        }
        return $numberOfNewTags;
    }

    /**
     * Update a tag in the database if necessary
     * Return true if the tag has been updated
     *
     * @param Tag $tagToUpdate
     * @param array $tagIds
     * @param array $tagNames
     * @return bool
     * @throws AppException
     */
    private function updateTag(Tag $tagToUpdate, array $tagIds, array $tagNames)
    {
        $tagToUpdateId = $tagToUpdate->getId();
        $tagToUpdateName = $tagToUpdate->getName();

        for ($i = 0, $size = count($tagIds); $i < $size; $i++) {
            if ($tagToUpdateId === $tagIds[$i] && $tagToUpdateName !== $tagNames[$i]) {
                $tagData = [
                    'id' => $tagToUpdateId,
                    'name' => $tagNames[$i]
                ];
                $updatedTag = new Tag($tagData);
                try {
                    $this->tagManager->edit($updatedTag);
                } catch (Exception $e) {
                    throw new AppException('Impossible to edit the tag ' . print_r($tagData, true), 0, $e);
                }
                return true;

            } elseif ($tagIds[$i] === 'new') {
                break;
            }
        }
        return false;
    }

    /**
     * Check if an Entity has to be deleted
     *
     * @param Entity $oldEntity
     * @param array $entityIdsToDelete
     * @return bool
     */
    private function isEntityToDelete(Entity $oldEntity, array $entityIdsToDelete): bool
    {
        foreach ($entityIdsToDelete as $entityIdToDelete) {
            if ($entityIdToDelete === $oldEntity->getId()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return Tag entities
     *
     * @return array
     */
    private static function getTagsFromForm(): ?array
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
     * Return Category entities
     *
     * @return array|null
     */
    private static function getCategoriesFromForm(): ?array
    {
        $categories = null;

        if (isset($_POST['categories'])) {
            foreach ($_POST['categories'] as $category) {
                $categories[] = new Category(['name' => $category]);
            }
        }

        return $categories;
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
            $post->setTitle($_POST['post-title']);
            $post->setExcerpt($_POST['post-excerpt']);
            $post->setContent($_POST['post-content']);
            $post->setAuthorId($_POST['post-author-id']);

            if (isset($_POST['add-post'])) {
                $post->setCreationDate(date(self::MYSQL_DATE_FORMAT));
            }

            // Edit a post
            if (isset($_POST['edit-post'])) {
                $post->setId($_POST['edit-post']);
                $post->setLastModificationDate(date(self::MYSQL_DATE_FORMAT));
            }
            if (isset($_POST['post-editor-id'])) {
                $post->setLastEditorId($_POST['post-editor-id']);
            }

            // Tags
            $tags = self::getTagsFromForm();
            if ($tags) {
                $post->setTags($tags);
            }

            // Categories
            $categories = self::getCategoriesFromForm();
            if ($categories) {
                $post->setCategories($categories);
            }

            // Markdown
            if (isset($_POST['markdown-content']) && !empty($_POST['markdown-content'])) {
                $post->setMarkdown(true);
            }

            return $post;

        } else {
            return null;
        }
    }

    /**
     * Create a Category from a form with $_POST
     *
     * @return Category|null
     */
    private function buildCategoryFromForm(): ?Category
    {
        $category = new Category();

        if (isset($_POST['category-name']) && !empty($_POST['category-name'])) {
            $category->setName(htmlspecialchars($_POST['category-name']));

            // Category to edit
            if (isset($_POST['edit-category'])) {
                $category->setId((int) $_POST['edit-category']);
            }

            return $category;

        } else {
            return null;
        }
    }

    /**
     * Create a Comment from $_POST
     *
     * @return Comment|null
     */
    private function buildCommentFromForm(): ?Comment
    {
        $comment = new Comment();

        if (isset($_POST['comment-id'])) {
            $comment->setId((int) $_POST['comment-id']);
        }

        if (isset($_POST['editor-id'])) {
            $comment->setLastEditorId((int) $_POST['editor-id']);
        }

        if (isset($_POST['creation-date'])) {
            $comment->setCreationDate($_POST['creation-date']);
        }

        if (isset($_POST['comment-approved'])) {
            $comment->setApproved(true);
        }

        if (isset($_POST['author-id'])) {
            $comment->setAuthorId((int) $_POST['author-id']);
        }

        if (isset($_POST['post-id'])) {
            $comment->setPostId((int) $_POST['post-id']);
        }

        if (isset($_POST['comment'])) {
            $comment->setContent($_POST['comment']);
        }

        if (isset($_POST['parent-id'])) {
            $comment->setParentId((int) $_POST['parent-id']);
        }

        return $comment;
    }

    /**
     * Cut the title and the excerpt of a post if they are too big. Return a message explaining the modifications.
     *
     * @param Post $post
     * @param string $message
     * @return string
     */
    private static function cutPost(Post $post, string $message = '')
    {
        // Title
        if (strlen($post->getTitle()) > PostManager::TITLE_LENGTH) {
            // We cut
            $post->setTitle(substr($post->getTitle(), 0, PostManager::TITLE_LENGTH));
            $message .= "Attention : le titre ne doit pas dépasser " . PostManager::TITLE_LENGTH . " caractères. Il a été coupé.<br>";
        }
        // Excerpt
        if (strlen($post->getExcerpt()) > PostManager::EXCERPT_LENGTH) {
            // We cut
            $post->setExcerpt(substr($post->getExcerpt(), 0, PostManager::EXCERPT_LENGTH));
            $message .= "Attention : l'extrait ne doit pas dépasser " . PostManager::EXCERPT_LENGTH . " caractères. Il a été coupé.<br>";
        }
        // Content
        if (strlen($post->getContent()) > PostManager::CONTENT_LENGTH) {
            // We cut
            $post->setContent(substr($post->getContent(), 0, PostManager::CONTENT_LENGTH));
            $message .= "Attention : le contenu ne doit pas dépasser " . PostManager::CONTENT_LENGTH . " caractères. Il a été coupé.<br>";
        }

        return $message;
    }

    /**
     * Convert markdown content
     *
     * @param string $content
     * @return string
     */
    private static function convertMarkdown(string $content)
    {
        return Markdown::defaultTransform($content);
    }

    /**
     * Add or edit a post and go back to the post editor with nice messages
     *
     * @param Post $postToHandle
     * @param bool $isNew
     * @throws BlogException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    private function handleAPost(Post $postToHandle, bool $isNew = true)
    {
        // Cut if title, excerpt or content are too big
        $message = self::cutPost($postToHandle);
        if (!empty($message)) {
            $messages[] = $message;
        }

        // Tags
        $tags = $postToHandle->getTags();

        if (!empty($tags)) {
            // Add tags in the database and get their ids
            $postToHandle->setTags($this->addNewTags($tags));
            $messages[] = "L'article sera visible avec #" . implode(' #', $postToHandle->getTags(true));
        }

        // Categories
        if (!empty($postToHandle->getCategories())) {
            foreach ($postToHandle->getCategories() as $category) {
                $categories[] = $this->categoryManager->getFromName($category->getName());
            }
            $postToHandle->setCategories($categories);
            $messages[] = "L'article a été publié dans : " . implode(', ', $postToHandle->getCategories(true));
        }

        if (empty($tags) && empty($postToHandle->getCategories())) {
            $messages[] = "L'article sera visible lorsque vous aurez choisi au moins une catégorie ou une étiquette";
        }

        if ($isNew) {
            array_unshift($messages, "L'article a été ajouté");
            $this->postManager->add($postToHandle);
        } else {
            array_unshift($messages, "L'article a été modifié");
            $this->postManager->edit($postToHandle);
        }

        // Come back to the admin panel
        $this->showPostEditor($this->postManager->getLastId(), implode('<br>', $messages));
    }
}
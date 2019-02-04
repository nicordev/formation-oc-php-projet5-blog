<?php
/**
 * Created by PhpStorm.
 * User: asus
 * Date: 23/01/2019
 * Time: 09:03
 */

namespace Controller;


use Application\Exception\AppException;
use Application\Exception\BlogException;
use Exception;
use Model\Entity\Category;
use Model\Entity\Entity;
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

    const VIEW_BLOG = 'blog/blog.twig';
    const VIEW_BLOG_POST = 'blog/blogPost.twig';
    const VIEW_BLOG_ADMIN = 'blog/blogAdmin.twig';
    const VIEW_POST_EDITOR = 'blog/postEditor.twig';
    const VIEW_CATEGORY_EDITOR = 'blog/categoryEditor.twig';

    const MYSQL_DATE_FORMAT = "Y-m-d H:i:s";

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
        $tags = $this->tagManager->getAll();
        $categories = $this->categoryManager->getAll();

        self::render(self::VIEW_BLOG_ADMIN, [
            'posts' => $posts,
            'message' => $message,
            'tags' => $tags,
            'categories' => $categories
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
        $availableTags = $this->tagManager->getAll();
        $availableTagNames = self::getTagNames($availableTags);
        $selectedTagNames = [];

        if ($postToEditId !== null) {
            $postToEdit = $this->postManager->get($postToEditId);
            $selectedTagNames = self::getTagNames($postToEdit->getTags());
        }

        self::render(self::VIEW_POST_EDITOR, [
            'postToEdit' => $postToEdit,
            'postToEditId' => $postToEditId,
            'message' => $message,
            'availableTags' => $availableTagNames,
            'selectedTags' => $selectedTagNames
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
     */
    public function showCategoryEditor(?int $categoryToEditId = null, string $message = '')
    {
        $categoryToEdit = null;
        $availableTags = $this->tagManager->getAll();
        $availableTagNames = self::getTagNames($availableTags);
        $selectedTagNames = [];

        if ($categoryToEditId !== null) {
            $categoryToEdit = $this->categoryManager->get($categoryToEditId);
            $selectedTagNames = self::getTagNames($categoryToEdit->getTags());
        }

        self::render(self::VIEW_CATEGORY_EDITOR, [
            'categoryToEdit' => $categoryToEdit,
            'categoryToEditId' => $categoryToEditId,
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
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addPost()
    {
        $newPost = self::buildPostFromForm();

        if ($newPost !== null) {
            $tags = $newPost->getTags();

            if (!empty($tags)) {
                // Add tags in the database and get their ids
                $newPost->setTags($this->addNewTags($tags));
            }

            $this->postManager->add($newPost);

            // Come back to the admin panel
            $this->showAdminPanel("Un article a été publié.");

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

        if ($modifiedPost !== null) {

            if (!empty($tags)) {
                // Add tags in the database and get their ids
                $modifiedPost->setTags($this->addNewTags($tags));
            }

            $this->postManager->edit($modifiedPost);
            // Come back to the admin panel
            $this->showAdminPanel("Un article a été modifié.");
        } else {
            // Try again...
            $this->showPostEditor(htmlspecialchars($_POST['edit-post']), "Erreur : le titre, l'extrait et le contenu de l'article ne doivent pas être vides.");
        }
    }

    /**
     * Delete a post from $_POST
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

    /**
     * Update the list of tags in the database
     *
     * @param array $tagIds
     * @param array $tagNames
     * @return bool
     * @throws Exception
     */
    public function updateTagList(?array $tagIds, ?array $tagNames)
    {
        if ($tagIds === null || $tagNames === null) {
            $this->tagManager->deleteAll();
            // Head back to the admin panel
            $this->showAdminPanel('La liste des étiquettes a été vidée.');
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
     * Add a new category from $_POST and add associated tags (note: a category must have a name and at least 1 associated tag)
     */
    public function addCategory()
    {
        $newCategory = self::buildCategoryFromForm();

        if ($newCategory !== null) {
            $tags = $newCategory->getTags();

            if (!empty($tags)) {
                // Add tags in the database and get their ids
                $newCategory->setTags($this->addNewTags($tags));
            } else {
                $this->showCategoryEditor(null, "Erreur : la catégorie doit être associée à au moins une étiquette.");
                return false;
            }

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
     * @return bool
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editCategory()
    {
        $modifiedCategory = self::buildCategoryFromForm();

        if ($modifiedCategory !== null) {
            $tags = $modifiedCategory->getTags();

            if (!empty($tags)) {
                // Add tags in the database and get their ids
                $modifiedCategory->setTags($this->addNewTags($tags));
            } else {
                $this->showCategoryEditor((int) $_POST['edit-category'], "Erreur : la catégorie doit être associée à au moins une étiquette.");
                return false;
            }

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
     */
    public function deleteCategory()
    {
        $categoryId = (int) $_POST['delete-category'];
        $this->categoryManager->delete($categoryId);
        // Come back to the admin panel
        $this->showAdminPanel("Une catégorie a été supprimée.");
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
                    throw new AppException('Impossible to add the tag ' . $tagNames[$i]);
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
                    throw new AppException('Impossible to edit the tag ' . print_r($tagData, true));
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

            if (isset($_POST['add-post'])) {
                $post->setCreationDate(date(self::MYSQL_DATE_FORMAT));
            }

            // Edit a post
            if (isset($_POST['edit-post'])) {
                $post->setId(htmlspecialchars($_POST['edit-post']));
                $post->setLastModificationDate(date(self::MYSQL_DATE_FORMAT));
            }
            if (isset($_POST['post-editor-id'])) {
                $post->setLastEditorId(htmlspecialchars($_POST['post-editor-id']));
            }

            // Tags
            $tags = self::getTagsFromForm();
            if ($tags) {
                $post->setTags($tags);
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

            // Tags
            $tags = self::getTagsFromForm();
            if ($tags) {
                $category->setTags($tags);
            }

            return $category;

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
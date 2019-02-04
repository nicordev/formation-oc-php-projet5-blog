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
        $availableTagNames = [];
        $selectedTagNames = [];

        foreach ($availableTags as $availableTag) {
            $availableTagNames[] = $availableTag->getName();
        }

        if ($postToEditId !== null) {
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
     * @throws \ReflectionException
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
        $tags = self::getTagsFromForm();

        if ($modifiedPost !== null) {

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
                $modifiedPost->setTags($tags);
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
     * @throws Exception
     */
    public function updateTagList(array $tagIds, array $tagNames)
    {
        $oldTags = $this->tagManager->getAll();

        // Add new tags
        $this->addNewTags($tagIds, $tagNames);

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
     * Update the list of categories in the database
     *
     * @param array $categoryIds
     * @param array $categoryNames
     * @throws AppException
     */
    public function updateCategoryList(array $categoryIds, array $categoryNames)
    {
        $oldCategories = $this->categoryManager->getAll();

        // Add new categories
        $this->addNewCategories($categoryIds, $categoryNames);

        // Update or delete categories
        $categoryIds = array_map('intval', $categoryIds); // Convert string to int
        foreach ($oldCategories as $oldCategory) {
            // Delete or update category ?
            if (self::isEntityToDelete($oldCategory, $categoryIds)) {
                $this->categoryManager->delete($oldCategory->getId());
            } else {
                $this->updateCategory($oldCategory, $categoryIds, $categoryNames);
            }
        }
        // Head back to the admin panel
        $this->showAdminPanel('La liste des catégories a été mise à jour.');
    }

    // Private

    /**
     * @param array $categoryIds
     * @param array $categoryNames
     * @return int
     * @throws AppException
     */
    private function addNewCategories(array $categoryIds, array $categoryNames)
    {
        $numberOfCategories = 0;

        for ($i = count($categoryIds) - 1; $i >= 0; $i--) {
            if ($categoryIds[$i] === 'new') {
                try {
                    $this->categoryManager->add(new Category(['name' => $categoryNames[$i]]));
                } catch (Exception $e) {
                    throw new AppException('Impossible to add the category ' . $categoryNames[$i]);
                }
                $numberOfCategories++;

            } else {
                break;
            }
        }
        return $numberOfCategories;
    }

    /**
     * Update a category in the database if necessary
     * Return true if the category has been updated
     *
     * @param Category $categoryToUpdate
     * @param array $categoryIds
     * @param array $categoryNames
     * @return bool
     * @throws AppException
     */
    private function updateCategory(Category $categoryToUpdate, array $categoryIds, array $categoryNames)
    {
        $categoryToUpdateId = $categoryToUpdate->getId();
        $categoryToUpdateName = $categoryToUpdate->getName();

        for ($i = 0, $size = count($categoryIds); $i < $size; $i++) {
            if ($categoryToUpdateId === $categoryIds[$i] && $categoryToUpdateName !== $categoryNames[$i]) {
                $categoryData = [
                    'id' => $categoryToUpdateId,
                    'name' => $categoryNames[$i]
                ];
                $updatedCategory = new Category($categoryData);
                try {
                    $this->categoryManager->edit($updatedCategory);
                } catch (Exception $e) {
                    throw new AppException('Impossible to edit the category ' . print_r($categoryData, true));
                }
                return true;

            } elseif ($categoryIds[$i] === 'new') {
                break;
            }
        }
        return false;
    }

    /**
     * Add new tags to the database if tag id === 'new'
     *
     * @param array $tagIds
     * @param array $tagNames
     * @return int
     * @throws AppException
     */
    private function addNewTags(array $tagIds, array $tagNames)
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
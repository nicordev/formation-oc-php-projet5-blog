<?php

namespace Controller;

use Helper\MemberHelper;
use Model\Entity\Member;
use Model\Entity\Post;
use Model\Entity\Tag;
use Application\Exception\AccessException;
use Application\Exception\AppException;
use Application\Exception\HttpException;
use Application\Exception\HttpException;
use Helper\BlogHelper;
use Exception;

class AdminController extends BlogController
{
    public const VIEW_BLOG_ADMIN = 'admin/blogAdmin.twig';
    public const VIEW_POST_EDITOR = 'admin/postEditor.twig';
    public const VIEW_CATEGORY_EDITOR = 'admin/categoryEditor.twig';
    public const VIEW_COMMENT_EDITOR = 'admin/commentEditor.twig';
    public const VIEW_MEDIA_LIBRARY = 'admin/mediaLibrary.twig';
    public const VIEW_IMAGE_EDITOR = 'admin/imageEditor.twig';

    public const KEY_YES_NO_FORM = 'yesNoForm';
    public const KEY_YES_ACTION = 'yesAction';
    public const KEY_NO_ACTION = 'noAction';
    public const KEY_MEMBERS = 'members';
    public const KEY_POST_TO_EDIT = 'postToEdit';
    public const KEY_POST_TO_EDIT_ID = 'postToEditId';
    public const KEY_AVAILABLE_TAGS = 'availableTags';
    public const KEY_SELECTED_TAGS = 'selectedTags';
    public const KEY_MARKDOWN = 'markdown';
    public const KEY_CATEGORY_TO_EDIT = 'categoryToEdit';
    public const KEY_CATEGORY_TO_EDIT_ID = 'categoryToEditId';
    public const KEY_COMMENT_TO_EDIT = 'commentToEdit';

    /**
     * Show the panel do manage blog posts
     *
     * @param string $message
     * @param array $yesNoForm
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AccessException
     */
    public function showAdminPanel(string $message = '', array $yesNoForm = [])
    {
        MemberController::verifyAccess();

        $posts = $this->postManager->getAll();
        $tags = $this->tagManager->getAll();
        $categories = $this->categoryManager->getAll();
        $comments = $this->commentManager->getAll();

        if (MemberHelper::memberConnected()) {
            if (in_array('admin', $_SESSION['connected-member']->getRoles())) {
                $members = $this->memberManager->getAll();
            }
        } else {
            throw new AccessException('No connected member found.');
        }

        $this->render(self::VIEW_BLOG_ADMIN, [
            self::KEY_POSTS => $posts,
            self::KEY_MESSAGE => $message,
            self::KEY_YES_NO_FORM => $yesNoForm,
            BlogController::KEY_TAGS => $tags,
            self::KEY_CATEGORIES => $categories,
            self::KEY_COMMENTS => $comments,
            self::KEY_MEMBERS => $members ?? null
        ]);
    }

    /**
     * Show the post editor
     *
     * @param int $postToEditId
     * @param string $message
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showPostEditor(?int $postToEditId = null, string $message = '')
    {
        if (!$postToEditId && isset($_POST[self::KEY_POST_ID])) {
            $postToEditId = (int) $_POST[self::KEY_POST_ID];
        } elseif (!$postToEditId && isset($_GET[self::KEY_POST_ID])) {
            if ($this->isAllowedToEditThePost((int) $_GET[self::KEY_POST_ID], $_SESSION['connected-member'])) {
                $postToEditId = (int) $_GET[self::KEY_POST_ID];
            }
        }

        $postToEdit = null;
        $categories = $this->categoryManager->getAll();
        $availableTags = $this->tagManager->getAll();
        $availableTagNames = BlogHelper::getTagNames($availableTags);
        $selectedTagNames = [];
        $markdown = false;

        if ($postToEditId !== null) {
            $postToEdit = $this->postManager->get($postToEditId);
            $selectedTagNames = BlogHelper::getTagNames($postToEdit->getTags());
            $markdown = $postToEdit->isMarkdown();
        }

        $this->render(self::VIEW_POST_EDITOR, [
            self::KEY_POST_TO_EDIT => $postToEdit,
            self::KEY_POST_TO_EDIT_ID => $postToEditId,
            self::KEY_CATEGORIES => $categories,
            self::KEY_MESSAGE => $message,
            self::KEY_AVAILABLE_TAGS => $availableTagNames,
            self::KEY_SELECTED_TAGS => $selectedTagNames,
            self::KEY_MARKDOWN => $markdown
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
     * @throws HttpException
     */
    public function showCategoryEditor(?int $categoryToEditId = null, string $message = '')
    {
        $categoryToEdit = null;

        if ($categoryToEditId !== null && $categoryToEditId > 0) {
            $categoryToEdit = $this->categoryManager->get($categoryToEditId);
        }

        $this->render(self::VIEW_CATEGORY_EDITOR, [
            self::KEY_CATEGORY_TO_EDIT => $categoryToEdit,
            self::KEY_CATEGORY_TO_EDIT_ID => $categoryToEditId,
            self::KEY_MESSAGE => $message
        ]);
    }

    /**
     * Show the comment editor page
     *
     * @param int|null $commentToEditId
     * @param string $message
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    public function showCommentEditor(?int $commentToEditId = null, string $message = '')
    {
        if (!$commentToEditId) {
            if (isset($_POST[self::KEY_COMMENT_ID]) && !empty($_POST[self::KEY_COMMENT_ID])) {
                $commentToEditId = (int) $_POST[self::KEY_COMMENT_ID];
            } else {
                throw new HttpException('It lacks the comment to edit id.');
            }
        }

        $comment = $this->commentManager->get($commentToEditId);

        $this->render(self::VIEW_COMMENT_EDITOR, [
            self::KEY_COMMENT_TO_EDIT => $comment
        ]);
    }

    // Actions

    /**
     * Add a new post from $_POST and add associated tags
     *
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addPost()
    {
        $newPost = BlogHelper::buildPostFromForm();
        $newPost->setCreationDate(date(self::MYSQL_DATE_FORMAT));
        $newPost->setLastModificationDate(date(self::MYSQL_DATE_FORMAT));
        $newPost->setLastEditorId($newPost->getAuthorId());

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
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editPost()
    {
        $modifiedPost = BlogHelper::buildPostFromForm();

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
     * @throws HttpException
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
     * @throws HttpException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AccessException
     */
    public function updateTagList(?array $tagIds, ?array $tagNames, ?string $action = null)
    {
        if ($tagIds === null || $tagNames === null) {
            if ($action === 'delete-all') {
                $this->tagManager->deleteAll();
                // Head back to the admin panel
                $this->showAdminPanel('Toutes les etiquettes ont été supprimées.');
            } else {
                // Head back to the admin panel
                $yesNoForm = [
                    self::KEY_YES_ACTION => '/admin/update-tags?action=delete-all',
                    self::KEY_NO_ACTION => '/admin'
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
            if (BlogHelper::isEntityToDelete($oldTag, $tagIds)) {
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
     *
     * @throws Exception
     */
    public function addCategory()
    {
        $newCategory = BlogHelper::buildCategoryFromForm();

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
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws HttpException
     * @throws AccessException
     * @throws \ReflectionException
     */
    public function editCategory()
    {
        $modifiedCategory = BlogHelper::buildCategoryFromForm();

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
     * @throws HttpException
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
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function addComment()
    {
        $comment = BlogHelper::buildCommentFromForm();

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
     *
     * @throws Exception
     */
    public function editComment()
    {
        $modifiedComment = BlogHelper::buildCommentFromForm();
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
     * @throws HttpException
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
     * Add new tags to the database if tag id === 'new'
     *
     * @param array $tagIds
     * @param array $tagNames
     * @return int
     * @throws HttpException
     */
    private function addNewTagsFromTagList(array $tagIds, array $tagNames)
    {
        $numberOfNewTags = 0;

        for ($i = count($tagIds) - 1; $i >= 0; $i--) {
            if ($tagIds[$i] === 'new') {
                try {
                    $this->tagManager->add(new Tag(['name' => $tagNames[$i]]));
                } catch (Exception $e) {
                    throw new HttpException('Impossible to add the tag ' . $tagNames[$i], 500, $e);
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
     * @throws HttpException
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
                    throw new HttpException('Impossible to edit the tag ' . print_r($tagData, true), 500, $e);
                }
                return true;

            } elseif ($tagIds[$i] === 'new') {
                break;
            }
        }
        return false;
    }

    /**
     * Add or edit a post and go back to the post editor with nice messages
     *
     * @param Post $postToHandle
     * @param bool $isNew
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws Exception
     */
    private function handleAPost(Post $postToHandle, bool $isNew = true)
    {
        // Cut if title, excerpt or content are too big
        $message = BlogHelper::cutPost($postToHandle);
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

            // Come back to the admin panel
            $this->showPostEditor($this->postManager->getLastId(), implode('<br>', $messages));
        } else {
            array_unshift($messages, "L'article a été modifié");
            $this->postManager->edit($postToHandle);

            // Come back to the admin panel
            $this->showPostEditor($postToHandle->getId(), implode('<br>', $messages));
        }
    }

    /**
     * Check if a member is allowed to edit a post (either an editor or the author)
     *
     * @param int $postId
     * @param Member $member
     * @return bool
     * @throws HttpException
     */
    public function isAllowedToEditThePost(int $postId, Member $member)
    {
        if (in_array(Member::EDITOR, $member->getRoles())) {
            return true;
        } elseif (in_array(Member::AUTHOR, $member->getRoles())) {
            $post = $this->postManager->get($postId);
            if ($post->getAuthorId() === $member->getId()) {
                return true;
            }
        }
        throw new HttpException("Access forbidden.", 403);
    }
}

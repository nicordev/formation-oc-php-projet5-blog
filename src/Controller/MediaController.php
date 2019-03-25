<?php

namespace Controller;

use Model\Entity\Post;
use Model\Entity\Tag;
use Application\Exception\AccessException;
use Application\Exception\AppException;
use Application\Exception\FileException;
use Application\FileHandler\ImageHandler;
use Application\Exception\HttpException;
use Application\Exception\PageNotFoundException;
use Helper\BlogHelper;
use Exception;

class MediaController extends Controller
{
    const VIEW_MEDIA_LIBRARY = 'admin/mediaLibrary.twig';
    const VIEW_IMAGE_EDITOR = 'admin/imageEditor.twig';

    /**
     * Show the media library
     *
     * @param string|null $message
     * @throws \Application\Exception\ImageException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showMediaLibrary(string $message = null)
    {
        $images = ImageHandler::getAllPath();

        $this->render(self::VIEW_MEDIA_LIBRARY, [
            'images' => $images,
            'message' => $message
        ]);
    }

    /**
     * Show image editor
     *
     * @param string|null $imagePath
     * @param string|null $message
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function showImageEditor(string $imagePath = null, string $message = null)
    {
        $this->render(self::VIEW_IMAGE_EDITOR, [
            'imagePath' => $imagePath,
            'message' => $message
        ]);
    }

    // Actions

    /**
     * Add an image in the library
     *
     * @throws \Application\Exception\ImageException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws AppException
     */
    public function addImage()
    {
        $message = null;
        try {
            $path = ImageHandler::uploadImage('new-image', '', 'blog_', '_post');
            $message = 'Le fichier a bien été ajouté dans ' . $path;
        } catch (FileException $e) {
            switch ($e->getCode()) {
                case 0:
                    $message = 'Erreur : est-ce que vous avez bien choisi un fichier ?';
                    break;
                case 1:
                    $message = "Erreur : l'extension du fichier n'est pas autorisée";
                    break;
                case 2:
                    $message = 'Erreur : le fichier est trop gros';
                    break;
                case 3:
                    $message = "Erreur : le fichier n'existe pas";
                    break;
                default:
                    throw new AppException("Erreur inconnue. Le code d'erreur n'existe pas.");
            }
        }

        $this->showMediaLibrary($message);
    }

    /**
     * Edit an image in the library
     *
     * @param string $imagePath
     * @param array $cropParameters
     * @param int $newHeight
     * @param int $newWidth
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function editImage(string $imagePath, array $cropParameters = [], int $newHeight = null, int $newWidth = null)
    {
        ImageHandler::editImage($imagePath, $cropParameters, $newHeight, $newWidth);

        $this->showImageEditor($imagePath, "L'image a été modifiée");
    }

    /**
     * Delete an image from the folder
     *
     * @param string $imagePath
     * @throws \Application\Exception\ImageException
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function deleteImage(string $imagePath)
    {
        ImageHandler::deleteImage($imagePath);

        $this->showMediaLibrary();
    }

}

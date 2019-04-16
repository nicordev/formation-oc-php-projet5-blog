<?php

namespace Controller;


use Application\Exception\AppException;
use Application\Exception\FileException;
use Application\FileHandler\ImageHandler;

class MediaController extends Controller
{
    const VIEW_MEDIA_LIBRARY = 'admin/mediaLibrary.twig';
    const VIEW_IMAGE_EDITOR = 'admin/imageEditor.twig';

    const KEY_IMAGES = "images";
    const KEY_IMAGE_PATH = "imagePath";

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
            self::KEY_IMAGES => $images,
            BlogController::KEY_MESSAGE => $message
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
            self::KEY_IMAGE_PATH => $imagePath,
            BlogController::KEY_MESSAGE => $message
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

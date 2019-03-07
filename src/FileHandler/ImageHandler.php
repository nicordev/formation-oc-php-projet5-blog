<?php

namespace Application\FileHandler;

use Application\Exception\FileException;
use Intervention\Image\ImageManagerStatic as Image;

class ImageHandler extends FileHandler
{
    const UPLOAD_FOLDER = ROOT_PATH . '/upload/';

    private function __construct()
    {
        // Disabled
    }

    /**
     * Upload an image on the server
     *
     * @param string $fieldName
     * @param string $fileName
     * @param string $prefix
     * @param string $suffix
     * @return string
     * @throws FileException
     */
    public static function uploadImage(string $fieldName, string $fileName = '', string $prefix = '', string $suffix = '')
    {
        return parent::upload($fieldName, self::UPLOAD_FOLDER, ['jpg', 'jpeg', 'gif', 'png'], $fileName, $prefix, $suffix);
    }

    /**
     * Resize or crop an image and save it
     *
     * @param string $path
     * @param array $cropParams
     * @param int|null $newHeight
     * @param int|null $newWidth
     */
    public static function editImage(string $path, array $cropParams = [], ?int $newHeight = null, ?int $newWidth = null)
    {
        $img = Image::make($path);

        if (
            isset($cropParams['width']) &&
            isset($cropParams['height'])
        ) {
            $img->crop($cropParams['width'], $cropParams['height'], $cropParams['x'] ?? null, $cropParams['y'] ?? null);
        }

        if (!$newHeight && $newWidth || !$newWidth && $newHeight) {
            $img->resize($newWidth, $newHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
        } elseif ($newWidth && $newHeight) {
            $img->resize($newWidth, $newHeight);
        }

        $img->save();
    }
}
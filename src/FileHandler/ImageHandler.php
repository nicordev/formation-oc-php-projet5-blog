<?php

namespace Application\FileHandler;

use Application\Exception\FileException;
use Application\Exception\ImageException;
use Intervention\Image\ImageManagerStatic as Image;

class ImageHandler extends FileHandler
{
    const UPLOAD_FOLDER = '/upload/';

    private function __construct()
    {
        // Disabled
    }

    /**
     * Get all images path
     *
     * @param string|null $hint
     * @param int|null $start
     * @param int|null $quantity
     * @return array|bool
     * @throws ImageException
     */
    public static function getAllPath(string $hint = null, int $start = 0, int $quantity = null)
    {
        $allImages = self::readImageFolder($hint);

        if (empty($allImages)) {
            return [];
        }

        $size = count($allImages);
        $i = $start;
        $counter = 0;

        if ($start >= $size) {
            throw new ImageException('$start > number of files in' . self::UPLOAD_FOLDER);
        }

        if ($quantity) {
            $images = [];
            while ($counter < $quantity && $i < $size) {
                $images[] = self::UPLOAD_FOLDER . $allImages[$i];
                $i++;
                $counter++;
            }
            return $images;
        }

        for (; $i < $size; $i++) {
            $allImages[$i] = self::UPLOAD_FOLDER . $allImages[$i];
        }
        return $allImages;
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
        return self::upload($fieldName, ROOT_PATH . self::UPLOAD_FOLDER, ['jpg', 'jpeg', 'gif', 'png'], $fileName, $prefix, $suffix);
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
            isset($cropParams['width']) && !empty($cropParams['width']) ||
            isset($cropParams['height']) &&  !empty($cropParams['height'])
        ) {
            $img->crop($cropParams['width'] ?? null, $cropParams['height'] ?? null, $cropParams['x'] ?? null, $cropParams['y'] ?? null);
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

    /**
     * Delete an image from the folder
     *
     * @param string $imagePath
     */
    public static function deleteImage(string $imagePath)
    {
        self::deleteFile(ROOT_PATH . $imagePath);
    }

    // Private

    /**
     * Get images file names
     *
     * @param string|null $hint
     * @return array|bool
     */
    private static function readImageFolder(?string $hint)
    {
        $allImages = parent::readFolder(ROOT_PATH . self::UPLOAD_FOLDER);

        if ($hint) {
            $images = [];
            for ($i = 0, $size = count($allImages); $i < $size; $i++) {
                if (strpos($allImages[$i], $hint) !== false) {
                    $images[] = $allImages[$i];
                }
            }
            return $images;
        }
        return $allImages;
    }
}
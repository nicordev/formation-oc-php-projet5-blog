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
        $allImages = parent::readFolder(ROOT_PATH . self::UPLOAD_FOLDER);

        if (empty($allImages)) {
            return [];
        }

        $size = count($allImages);
        $i = $start;
        $counter = 0;

        if ($start >= $size) {
            throw new ImageException('$start > number of files or no images found');
        }

        if ($hint) {
            $images = [];
            if ($quantity) {
                for (; $i < $size; $i++) {
                    if (strpos($allImages[$i], $hint) !== false) {
                        $images[] = self::UPLOAD_FOLDER . $allImages[$i];
                        $counter++;
                        if ($counter >= $quantity) {
                            return $images;
                        }
                    }
                }
            } else {
                for (; $i < $size; $i++) {
                    if (strpos($allImages[$i], $hint) !== false) {
                        $images[] = self::UPLOAD_FOLDER . $allImages[$i];
                    }
                }
            }
            return $images;
        }

        if ($quantity) {
            $images = [];
            while ($counter < $quantity) {
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
        return parent::upload($fieldName, ROOT_PATH . self::UPLOAD_FOLDER, ['jpg', 'jpeg', 'gif', 'png'], $fileName, $prefix, $suffix);
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

    // Private

    private static function readImageFolder(?string $hint)
    {
        $allImages = parent::readFolder(ROOT_PATH . self::UPLOAD_FOLDER);
        $images = [];

        if ($hint) {
            for ($i = 0, $size = count($allImages); $i < $size; $i++) {
                if (strpos($allImages[$i], $hint) !== false) {
                    $images[] = self::UPLOAD_FOLDER . $allImages[$i];
                }
            }
        }

        return $images;
    }
}
<?php

namespace Application\FileHandler;

class FileHandler
{
    public function add(string $fieldName, string $destinationFolder, array $authorizedExtensions = [], string $fileName = '', string $prefix = '', string $suffix = '')
    {
        if (isset($_FILES[$fieldName]) AND $_FILES[$fieldName]['error'] == 0)
        {
            if ($_FILES[$fieldName]['size'] <= 8000000)
            {
                $fileInfo = pathinfo($_FILES[$fieldName]['name']);
                $extensionUpload = strtolower($fileInfo['extension']);

                if (empty($fileName)) {
                    $destination = $destinationFolder . $prefix . basename($_FILES[$fieldName]['name']) . $suffix;
                } else {
                    $destination = $destinationFolder . $prefix . $fileName . $suffix;
                }

                if (!empty($authorizedExtensions) && in_array($extensionUpload, $authorizedExtensions))
                {
                    move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination);
                }
            }
        }
    }
}
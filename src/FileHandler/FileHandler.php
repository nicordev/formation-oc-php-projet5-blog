<?php

namespace Application\FileHandler;

use Application\Exception\FileException;

class FileHandler
{
    private function __construct()
    {
        // Disabled
    }

    /**
     * Get a file
     *
     * @param string $path
     * @param string $mode
     * @return bool|resource
     */
    public static function getFile(string $path, string $mode = 'r')
    {
        return fopen($path, $mode);
    }

    /**
     * Get the file names of a folder
     *
     * @param string $path
     * @return array|bool
     */
    public static function readFolder(string $path)
    {
        $files = [];
        $i = 0;
        if ($dir = opendir($path)) {
            while (($file = readdir($dir)) !== false) {
                if ($file != '.' && $file != '..') { // Avoid parent and current folder
                    if (!is_dir($file)) { // Avoid folders
                        $files[] = $file;
                        $i++;
                    }
                }
            }
            closedir($dir);
            return $files;
        }
        return false;
    }

    /**
     * Delete a file
     *
     * @param string $path
     * @return bool
     */
    public static function deleteFile(string $path)
    {
        return unlink($path);
    }

    /**
     * Upload a file on the server
     *
     * @param string $fieldName
     * @param string $destinationFolder
     * @param array $authorizedExtensions
     * @param string $fileName
     * @param string $prefix
     * @param string $suffix
     * @return string the path to the file on the server
     * @throws FileException
     */
    public static function upload(string $fieldName, string $destinationFolder, array $authorizedExtensions = [], string $fileName = '', string $prefix = '', string $suffix = '')
    {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] == 0)
        {
            if ($_FILES[$fieldName]['size'] <= 8000000)
            {
                $fileInfo = pathinfo($_FILES[$fieldName]['name']);
                
                if (empty($fileName)) {
                    $fileName = $fileInfo['filename'];
                }
                $fileExtension = strtolower($fileInfo['extension']);
                $destination = $destinationFolder . $prefix . $fileName . $suffix . '.' . $fileExtension;

                if (!empty($authorizedExtensions) && in_array($fileExtension, $authorizedExtensions) || empty($authorizedExtensions))
                {
                    if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $destination)) {
                        return $destination;
                    } else {
                        throw new FileException('An error occured when move_uploaded_file(' . $destination . ')');
                    }
                }
                throw new FileException('The file extension ' . $fileExtension . ' is not allowed');

            } else {
                throw new FileException('The file is too big');
            }
        }
        throw new FileException('The file does not exists or an error occured ' . $_FILES[$fieldName]['error'] ?? '');
    }

    /**
     * Convert a path either for windows or linux
     *
     * @param string $path
     * @param bool $toWindows
     * @return mixed
     */
    public static function convertPath(string $path, bool $toWindows = false) {
            if ($toWindows) {
                return str_replace('/', '\\', $path);
            }
            return str_replace('\\', '/', $path);
    }
}
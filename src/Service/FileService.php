<?php

namespace App\Service;

use Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{

    /**
     * Save file
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string $filename
     */
    public function save(UploadedFile $file, $directory)
    {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

        try {
            $file->move($directory, $fileName);
        } catch (FileException $e) {
            throw new Exception('Erreur lors de l\upload du fichier');
        }

        return $fileName;
    }
}

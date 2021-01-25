<?php

namespace App\Service;

use Exception;

class Picture
{

    public function crop($filePath)
    {
        $resource = imagecreatefromjpeg($filePath);
        $size = imagesx($resource);
        $ressource2 = imagecrop($resource, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => ($size / 1.5)]);

        if ($ressource2 != FALSE) {
            try {
                imagejpeg($ressource2, $filePath);
                imagedestroy($ressource2);
            } catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }
        }
    }
}

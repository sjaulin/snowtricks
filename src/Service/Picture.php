<?php

namespace App\Service;

use Exception;

class Picture
{

    public function crop($filePath, $ratio)
    {
        $resource = imagecreatefromjpeg($filePath);
        $size = imagesx($resource);
        $ressource2 = imagecrop($resource, ['x' => 0, 'y' => 0, 'width' => $size, 'height' => ($size / $ratio)]);

        if ($ressource2 != false) {
            try {
                imagejpeg($ressource2, $filePath);
                imagedestroy($ressource2);
            } catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }
        }
    }

    public function scale($filePath, $w, $h)
    {
        $resource = imagecreatefromjpeg($filePath);
        $ressource2 = imagescale($resource, $w, $h);
        if ($ressource2 != false) {
            try {
                imagejpeg($ressource2, $filePath);
                imagedestroy($ressource2);
            } catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }
        }
    }
}

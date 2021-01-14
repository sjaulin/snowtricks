<?php

namespace App\EntityListener;

use App\Entity\Picture;

/**
 * Class PictureListener
 * @package App\EntityListener
 */
class PictureListener
{
    /**
     * @var string
     */
    private $uploadDirAbsolutePath;

    /**
     * ImageListener constructor.
     * @param string $uploadDirAbsolutePath
     */
    public function __construct(string $uploadDirAbsolutePath)
    {
        $this->uploadDirAbsolutePath = $uploadDirAbsolutePath;
    }

    /**
     * @param Picture $picture
     */
    public function prePersist(Picture $picture)
    {
        dd('test Picture Listener');

        if ($picture->getUploadedFile() === null) {
            return;
        }
        $filename = md5(uniqid("", true)) . "." . $picture->getUploadedFile()->guessExtension();
        $picture->getUploadedFile()->move($this->uploadDirAbsolutePath, $filename);
        $picture->setPath($filename);
    }
}

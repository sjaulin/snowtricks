<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PictureRepository::class)
 * @ORM\EntityListeners({"App\EntityListener\PictureListener"})
 */
class Picture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column
     */
    private $path;

    /**
     * @var UploadedFile|null
     * @Assert\Image
     * @Assert\NotNull(groups={"add"})
     */
    private $uploadedFile;

    /**
     * @var Trick|null
     * @ORM\ManyToOne(targetEntity="Trick", inversedBy="pictures")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $trick;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return null|string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param null|string $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return null|UploadedFile
     */
    public function getUploadedFile(): ?UploadedFile
    {
        return $this->uploadedFile;
    }

    /**
     * @param null|UploadedFile $uploadedFile
     */
    public function setUploadedFile(?UploadedFile $uploadedFile): void
    {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * @return Trick|null
     */
    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    /**
     * @param Trick|null $trick
     */
    public function setTrick(?Trick $trick): void
    {
        $this->trick = $trick;
    }
}

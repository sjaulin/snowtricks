<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @UniqueEntity("name", message="La catégorie {{ value }} existe déjà")
 */
class Category
{

    const CONSTRAINT_NAME_LENGTH_MIN = 2;
    const CONSTRAINT_NAME_LENGTH_MAX = 20;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=Trick::CONSTRAINT_NAME_LENGTH_MAX, unique=true)
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(
     * min=Trick::CONSTRAINT_NAME_LENGTH_MIN,
     * max=Trick::CONSTRAINT_NAME_LENGTH_MAX,
     * minMessage="Le nom doit faire au moins {{ limit }} caractères",
     * maxMessage="Le nom doit faire au maximum {{ limit }} caractères")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Trick::class, mappedBy="category")
     */
    private $Tricks;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    public function __construct()
    {
        $this->Tricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->Tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->Tricks->contains($trick)) {
            $this->Tricks[] = $trick;
            $trick->setCategory($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->Tricks->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getCategory() === $this) {
                $trick->setCategory(null);
            }
        }

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}

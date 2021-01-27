<?php

namespace App\Listener\Entity;

use App\Entity\Category;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Category $category, LifecycleEventArgs $event)
    {
        $category->computeSlug($this->slugger);
    }

    public function preUpdate(Category $category, LifecycleEventArgs $event)
    {
        $category->computeSlug($this->slugger);
    }
}

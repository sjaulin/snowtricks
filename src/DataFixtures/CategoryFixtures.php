<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public const CATEGORY_REFERENCE = 'category-saut';

    public function load(ObjectManager $manager)
    {
        $category = new Category();
        $category->setName('Saut simple');
        $this->addReference(self::CATEGORY_REFERENCE, $category);
        $manager->persist($category);
        $manager->flush();
    }
}

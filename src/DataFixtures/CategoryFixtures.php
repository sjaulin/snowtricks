<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class CategoryFixtures extends Fixture implements FixtureGroupInterface
{
    const CATEGORY_NB = 2;

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::CATEGORY_NB; $i++) {
            $category = new Category();
            $category->setName('categorie ' . $i);
            $this->addReference('category_' . $i, $category);
            $manager->persist($category);
            $manager->flush();
        }
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}

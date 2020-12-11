<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface; // Gère les dépendances entre fixtures.
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{
    public const CATEGORY_REFERENSE = 'admin-user';

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 1; $i <= 10; $i ++) {
            $trick = new Trick();
            $trick->setTitle($faker->name);
            $trick->setDescription($faker->text(150));
            $trick->setCreatedAt(new \DateTime);
            $trick->setCategory($this->getReference(CategoryFixtures::CATEGORY_REFERENCE));
            $manager->persist($trick);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            CategoryFixtures::class,
        );
    }
}

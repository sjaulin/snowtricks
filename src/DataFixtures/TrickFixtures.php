<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Trick;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\CategoryFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class TrickFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    const TRICK_NB = 10;

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = [];
        for ($i = 0; $i < CategoryFixtures::CATEGORY_NB; $i++) {
            $categories[] = $this->getReference('category_' . $i);
        }

        $users = [];
        for ($i = 0; $i < UserFixtures::USER_NB; $i++) {
            $users[] = $this->getReference('user_' . $i);
        }

        for ($i = 0; $i < self::TRICK_NB; $i++) {
            shuffle($users);
            shuffle($categories);
            $trick = new Trick();
            $trick->setName(ucwords($faker->unique()->word()))
                ->setOwner($users[0])
                ->setDescription($faker->text(150))
                ->setCreatedAt(new \DateTime)
                ->setCategory($categories[0]);
            $this->addReference('trick_' . $i, $trick);
            $manager->persist($trick);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function getDependencies()
    {
        return array(
            CategoryFixtures::class,
            UserFixtures::class,
        );
    }
}

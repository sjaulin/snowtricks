<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class InitFixtures extends Fixture implements FixtureGroupInterface
{


    public function load(ObjectManager $manager)
    {
        $dirs = array(
            'public/uploads',
            'public/uploads/user',
            'public/uploads/trick'
        );

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir);
            }
        }
    }

    public static function getGroups(): array
    {
        return ['test', 'app'];
    }
}

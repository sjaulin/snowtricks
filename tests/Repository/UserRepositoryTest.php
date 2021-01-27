<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\kernelTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class UserRepositoryTest extends kernelTestCase
{
    use FixturesTrait;

    public function testCount()
    {

        self::bootkernel();
        $this->loadFixtures([
            UserFixtures::class,
        ]);
        $users = self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(6, $users);
    }
}

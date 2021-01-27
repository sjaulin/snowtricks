<?php

namespace App\Tests\Repository;

use App\DataFixtures\CategoryFixtures;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\kernelTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;

class CategoryRepositoryTest extends kernelTestCase
{
    use FixturesTrait;

    public function testCount()
    {
        self::bootkernel();
        $this->loadFixtures([
            CategoryFixtures::class,
        ]);

        $categories = self::$container->get(CategoryRepository::class)->count([]);
        $this->assertEquals(2, $categories);
    }
}

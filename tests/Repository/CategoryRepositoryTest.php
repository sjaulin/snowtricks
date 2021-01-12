<?php

namespace App\Tests\Repository;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Test\kernelTestCase;

class CategoryRepositoryTest extends kernelTestCase
{
    public function testCount() {
        self::bootkernel();
        $categories = self::$container->get(CategoryRepository::class)->count([]);
        $this->assertEquals(4, $categories);
    }
}


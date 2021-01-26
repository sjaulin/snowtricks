<?php

namespace App\Tests\Repository;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Test\kernelTestCase;

class TrickRepositoryTest extends kernelTestCase
{
    public function testCount()
    {
        self::bootkernel();
        $tricks = self::$container->get(TrickRepository::class)->count([]);
        $this->assertEquals(10, $tricks);
    }
}

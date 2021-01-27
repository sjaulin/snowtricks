<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\DataFixtures\TrickFixtures;

class TrickControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testShowTrick()
    {
        $client = static::createClient();

        $this->loadFixtures([
            TrickFixtures::class
        ]);

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html .container h1', 'SnowTricks');
        $this->assertSame(10, $crawler->filter('div.picture')->count());
    }
}

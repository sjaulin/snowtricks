<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use App\DataFixtures\TrickFixtures;

class TrickControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testControllerTrick()
    {
        $client = static::createClient();

        $this->loadFixtures([
            TrickFixtures::class
        ]);

        // Homepage List
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('html .container h1', 'SnowTricks');
        $this->assertSame(4, $crawler->filter('div.picture')->count());

        // Connect
        $crawler = $client->request('GET', '/login');
        $client->submitForm('connexion', [
            'login[email]' => 'User0@gmail.com',
            'login[password]' => 'password'
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('html .navbar', 'Logout');

        // Create Trick
        $crawler = $client->request('GET', '/trick/new');
        $client->submitForm('Enregistrer', [
            'trick[name]' => 'Le trick de la mort',
            'trick[description]' => 'lorem ipsum'
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorTextContains('html .alert-success', 'Le trick a été créé');
        $this->assertSelectorTextContains('html h1', 'Le trick de la mort');
    }
}

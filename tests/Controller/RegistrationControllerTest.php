<?php

namespace App\Tests\Controller;

use App\DataFixtures\InitFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\kernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class RegistrationControllerTest extends WebTestCase
{
    use FixturesTrait;
    public function testRegistration()
    {
        $client = static::createClient();

        $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');

        $avatar =  dirname(__FILE__) . '/avatar.jpg';
        $client->submitForm('Enregistrer', [
            'registration_form[email]' => 'test@test.com',
            'registration_form[pseudo]' => 'test',
            'registration_form[plainPassword]' => 'password',
            'registration_form[agreeTerms]' => 1,
            'registration_form[avatar]' => $avatar,
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
    }
}

<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Avatar;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    const USER_NB = 5;

    private $params;
    protected $encoder;

    public function __construct(
        ParameterBagInterface $params,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->params = $params;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Factory::create('fr_FR');

        // ADMIN
        $admin = new User;
        $hash = $this->encoder->encodePassword($admin, 'password');
        $admin->setEmail('admin@gmail.com')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN'])
            ->setPseudo('admin')
            ->setIsVerified(true);
        $manager->persist($admin);

        // USERS
        $part = 'https://images.unsplash.com/';
        $part2 = '?ixlib=rb-0.3.5&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200';
        $fakerUsers = array(
            $part . 'photo-1506803682981-6e718a9dd3ee' . $part2 . '&s=c3a31eeb7efb4d533647e3cad1de9257',
            $part . 'photo-1495147334217-fcb3445babd5' . $part2 . '&s=7dc81c222437ff6fed90bfb04c491d6f',
            $part . 'photo-1509783236416-c9ad59bae472' . $part2 . '&ixid=eyJhcHBfaWQiOjE3Nzg0fQ',
            $part . 'photo-1510227272981-87123e259b17' . $part2 . '&s=3759e09a5b9fbe53088b23c615b6312e',
            $part . 'photo-1507120878965-54b2d3939100' . $part2 . '&s=99fbace66d1bfa48c9c6dc8afcac3aab'
        );
        for ($u = 0; $u < self::USER_NB; $u++) {
            $user = new User;
            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setEmail("User$u@gmail.com")
                ->setPseudo("User#$u")
                ->setPassword($hash)
                ->setIsVerified(true);

            $fileName = 'fake_' . $u . '.jpg';
            $filePath = $this->params->get('uploads_user_path') . '/' . $fileName;
            if (file_put_contents($filePath, file_get_contents($fakerUsers[$u]))) {
                $avatar = new Avatar;
                $avatar->setName($fileName);
                $user->setAvatar($avatar);
            }
            $this->addReference('user_' . $u, $user);
            $manager->persist($user);
        }
        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\Avatar;
use App\Service\ImageService;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use App\DataFixtures\InitFixtures;

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    private $params;
    protected $encoder;
    protected $imageService;

    public function __construct(
        ParameterBagInterface $params,
        UserPasswordEncoderInterface $encoder,
        ImageService $imageService
    ) {
        $this->params = $params;
        $this->encoder = $encoder;
        $this->imageService = $imageService;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        // ADMIN
        $admin = new User;

        // Encodera avec l'algorithm pour l'entité User indiqué dans le security.yaml
        $hash = $this->encoder->encodePassword($admin, 'password');

        $admin->setEmail('admin@gmail.com')
            ->setPassword($hash)
            ->setRoles(['ROLE_ADMIN'])
            ->setPseudo('admin')
            ->setIsVerified(true);
        $manager->persist($admin);

        // USERS
        $dir = $this->params->get('uploads_user_path');
        $fakerFiles = scandir($dir);
        $users = [];
        foreach ($fakerFiles as $key => $fakerFile) {
            if (is_file($dir . '/' . $fakerFile)) {
                $user = new User;
                $hash = $this->encoder->encodePassword($user, 'password');
                $user->setEmail("User$key@gmail.com")
                    ->setPseudo($faker->unique()->firstName())
                    ->setPassword($hash)
                    ->setIsVerified(true);

                $avatar = new Avatar;
                $avatar->setName($fakerFile);
                $user->setAvatar($avatar);
                $manager->persist($avatar);

                $manager->persist($user);
                $users[] = $user;
            }
        }

        // TRICKS
        $dir = $this->params->get('uploads_trick_path');
        $fakerFiles = scandir($dir);
        $picturesList = [];
        foreach ($fakerFiles as $key => $fakerFile) {
            if (is_file($dir . '/' . $fakerFile)) {
                $picturesList[] = $fakerFile;
            }
        }
        $videosList = array(
            'https://www.youtube.com/embed/zWxBgxq5rP0',
            'https://www.dailymotion.com/embed/video/x7w6v2m'
        );

        for ($c = 0; $c < 2; $c++) {
            // Categories
            $category = new Category();
            $category->setName(ucwords($faker->unique()->word()));
            $manager->persist($category);

            // Tricks in category
            for ($t = 0; $t < 20; $t++) {
                shuffle($users);
                $trick = new Trick();
                $trick->setName(ucwords($faker->unique()->word()))
                    ->setOwner($users[0])
                    ->setDescription($faker->text(rand(320, 455)))
                    ->setCreatedAt(new \DateTime)
                    ->setCategory($category);

                // Pictures
                for ($p = 0; $p < 5; $p++) {
                    shuffle($picturesList);
                    $fileName = $picturesList[0];
                    $filePath = $dir . '/' . $fileName;
                    $this->imageService->crop($filePath, 1.5);
                    $picture = new Picture;
                    $picture->setName($fileName);
                    $trick->addPicture($picture);
                }
                // Videos
                for ($v = 0; $v < 3; $v++) {
                    shuffle($videosList);
                    $video = new Video;
                    $video->setUrl($videosList[0]);
                    $trick->addVideo($video);
                }

                // Comments
                for ($u = 0; $u < rand(4, 20); $u++) {
                    shuffle($users);
                    $comment = new Comment;
                    $comment->setMessage($faker->unique()->text(150));
                    $comment->setUser($users[0]);
                    $manager->persist($comment);
                    $trick->addComment($comment);
                }

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['app'];
    }
}

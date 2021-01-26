<?php

namespace App\DataFixtures;

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
use App\Service\Picture as PictureService;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $params;
    protected $encoder;
    protected $pictureService;

    public function __construct(
        ParameterBagInterface $params,
        UserPasswordEncoderInterface $encoder,
        PictureService $pictureService
    ) {
        $this->params = $params;
        $this->encoder = $encoder;
        $this->pictureService = $pictureService;
    }

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
        $part = 'https://images.unsplash.com/';
        $part2 = '?ixlib=rb-0.3.5&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200';
        $fakerUsers = array(
            $part . 'photo-1506803682981-6e718a9dd3ee' . $part2 . '&s=c3a31eeb7efb4d533647e3cad1de9257',
            $part . 'photo-1495147334217-fcb3445babd5' . $part2 . '&s=7dc81c222437ff6fed90bfb04c491d6f',
            $part . 'photo-1509783236416-c9ad59bae472' . $part2 . '&ixid=eyJhcHBfaWQiOjE3Nzg0fQ',
            $part . 'photo-1510227272981-87123e259b17' . $part2 . '&s=3759e09a5b9fbe53088b23c615b6312e',
            $part . 'photo-1507120878965-54b2d3939100' . $part2 . '&s=99fbace66d1bfa48c9c6dc8afcac3aab'
        );
        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $avatar = new Picture;
            $user = new User;
            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setEmail("User$u@gmail.com")
                ->setPseudo($faker->unique()->firstName())
                ->setPassword($hash)
                ->setIsVerified(true);

            $fileName = 'fake_' . $u . '.jpg';
            $filePath = $this->params->get('uploads_user_path') . '/' . $fileName;
            if (file_put_contents($filePath, file_get_contents($fakerUsers[$u]))) {
                $avatar = new Avatar;
                $avatar->setName($fileName);
                $user->setAvatar($avatar);
                $manager->persist($avatar);
            }

            $manager->persist($user);
            $users[] = $user;
        }

        // TRICKS
        $picturesList = array(
            'https://source.unsplash.com/QpD_ArXWKLA/960x640',
            'https://source.unsplash.com/jyoVp3TxTZk/960x640',
            'https://source.unsplash.com/YKikzmEOJXM/960x640',
            'https://source.unsplash.com/9SGGun3iIig/960x640',
            'https://source.unsplash.com/LXswmpRHORY/960x640',
            'https://source.unsplash.com/11SF6RqgXXw/960x640',
            'https://source.unsplash.com/H0swuAwbYHk/960x640',
            'https://source.unsplash.com/bpz-MQJDJuA/960x640',
            'https://source.unsplash.com/HLvFLhyOSVs/960x640',
            'https://source.unsplash.com/xrdI3SzgOAo/960x640',
            'https://source.unsplash.com/Mn6KHffcfMo/960x640',
            'https://source.unsplash.com/XgaHKSV4UBA/960x640',
            'https://source.unsplash.com/awpIAe9P53E/960x640',
            'https://source.unsplash.com/WdBQHcIiSIw/960x640',
            'https://cdn.pixabay.com/photo/2016/03/27/18/40/snow-1283525_960_720.jpg',
            'https://cdn.pixabay.com/photo/2014/12/02/14/12/snowboarding-554048_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/03/27/18/40/snow-1283525_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/03/26/10/01/snowboarder-690779_960_720.jpg',
            'https://cdn.pixabay.com/photo/2013/12/12/21/28/snowboard-227541_960_720.jpg',
            'https://cdn.pixabay.com/photo/2016/01/26/00/26/canazei-1161799_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/09/11/09/25/snowboard-3668972_960_720.jpg',
            'https://cdn.pixabay.com/photo/2015/02/01/05/51/bungee-jumping-619139_960_720.jpg',
            'https://cdn.pixabay.com/photo/2013/12/12/21/28/snowboard-227540_960_720.jpg',
            'https://cdn.pixabay.com/photo/2018/03/10/15/22/snow-3214256_960_720.jpg'
        );

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
            for ($t = 0; $t < 5; $t++) {
                $trick = new Trick();
                shuffle($users);
                $trick->setName(ucwords($faker->unique()->word()))
                    ->setOwner($users[0])
                    ->setDescription($faker->text(150))
                    ->setCreatedAt(new \DateTime)
                    ->setCategory($category);

                // Pictures
                shuffle($picturesList);
                for ($p = 0; $p < 2; $p++) {
                    $fileName = $c . '_' . $t . '_' . $p . '.jpg';
                    $filePath = $this->params->get('uploads_trick_path') . '/' . $fileName;
                    $url = !empty($picturesList[$p]) ? $picturesList[$p] : $picturesList[0];
                    if (file_put_contents($filePath, file_get_contents($url))) {
                        $this->pictureService->crop($filePath);
                        $picture = new Picture;
                        $picture->setName($fileName);
                        $trick->addPicture($picture);
                    }
                }
                // Videos
                shuffle($videosList);
                for ($v = 0; $v < 2; $v++) {
                    $video_url = !empty($videosList[$v]) ? $videosList[$v] : $videosList[0];
                    $video = new Video;
                    $video->setUrl($video_url);
                    $trick->addVideo($video);
                }

                // Comments
                shuffle($users);
                for ($u = 0; $u < rand(1, 3); $u++) {
                    $comment = new Comment;
                    $comment->setMessage($faker->unique()->text(150));
                    $comment->setUser($users[$u]);
                    $manager->persist($comment);
                    $trick->addComment($comment);
                }

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }
}

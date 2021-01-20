<?php

namespace App\DataFixtures;

use App\Entity\Avatar;
use Faker\Factory;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $params;
    protected $slugger;
    protected $encoder;

    public function __construct(
        ParameterBagInterface $params,
        SluggerInterface $slugger,
        UserPasswordEncoderInterface $encoder
    ) {
        $this->params = $params;
        $this->slugger = $slugger;
        $this->encoder = $encoder;
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
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // USERS
        $avatar_list = array(
            'https://images.unsplash.com/photo-1506803682981-6e718a9dd3ee?ixlib=rb-0.3.5&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200&s=c3a31eeb7efb4d533647e3cad1de9257',
            'https://images.unsplash.com/photo-1495147334217-fcb3445babd5?ixlib=rb-0.3.5&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200&s=7dc81c222437ff6fed90bfb04c491d6f',
            'https://images.unsplash.com/photo-1509783236416-c9ad59bae472?ixlib=rb-1.2.1&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200&ixid=eyJhcHBfaWQiOjE3Nzg0fQ',
            'https://images.unsplash.com/photo-1510227272981-87123e259b17?ixlib=rb-0.3.5&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200&s=3759e09a5b9fbe53088b23c615b6312e',
            'https://images.unsplash.com/photo-1507120878965-54b2d3939100?ixlib=rb-0.3.5&q=80&fm=jpg&crop=faces&fit=crop&h=200&w=200&s=99fbace66d1bfa48c9c6dc8afcac3aab'
        );
        $users_list = [];
        for ($u = 0; $u < 5; $u++) {
            $avatar = new Picture;
            $user = new User;
            $hash = $this->encoder->encodePassword($user, 'password');
            $user->setEmail("User$u@gmail.com")
                ->setPassword($hash);

            $file_name = 'fake_' . $u . '.jpg';
            if ($this->_upload_file($avatar_list[$u], $this->params->get('uploads_user_path'), $file_name)) {
                $avatar = new Avatar;
                $avatar->setName($file_name);
                $user->setAvatar($avatar);
                $manager->persist($avatar);
            }

            $manager->persist($user);
            $users_list[] = $user;
        }

        // TRICKS
        $pictures_list = array(
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

        for ($c = 0; $c < 2; $c++) {

            // Categories
            $category = new Category();
            $category->setName(ucwords($faker->unique()->word()));
            $category->setSlug($this->slugger->slug(strtolower($category->getName())));
            $manager->persist($category);

            // Tricks
            for ($t = 0; $t < 5; $t++) {
                $trick = new Trick();

                $trick->setName(ucwords($faker->unique()->word()))
                    ->setDescription($faker->text(150))
                    ->setCreatedAt(new \DateTime)
                    ->setSlug($this->slugger->slug(strtolower($trick->getName())))
                    ->setCategory($category);

                // Pictures
                shuffle($pictures_list);

                for ($p = 0; $p < 2; $p++) {
                    $unsplash_url = !empty($pictures_list[$p]) ? $pictures_list[$p] : $pictures_list[0];
                    $file_name = $c . '_' . $t . '_' . $p . '.jpg';
                    if ($this->_upload_file($unsplash_url, $this->params->get('uploads_trick_path'), $file_name)) {
                        $picture = new Picture;
                        $picture->setName($file_name);
                        $trick->addPicture($picture);
                    }
                }

                // Comments
                shuffle($users_list);

                for ($u = 0; $u < 3; $u++) {
                    $comment = new Comment;
                    $comment->setMessage($faker->unique()->text(150));
                    $comment->setUser($users_list[$u]);
                    $comment->setTrick($trick);
                    $manager->persist($comment);
                }

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }

    /**
     * Upload url file & save on uploads_directory.
     */
    private function _upload_file($url, $directory, $file_name)
    {
        $filename_path =  $directory . '/' . $file_name;
        if (file_put_contents($filename_path, file_get_contents($url))) {
            return true;
        }
        return false;
    }
}

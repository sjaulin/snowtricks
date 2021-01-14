<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Trick;
use App\Entity\Picture;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class AppFixtures extends Fixture
{
    private $params;
    protected $slugger;

    public function __construct(ParameterBagInterface $params, SluggerInterface $slugger)
    {
        $this->params = $params;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $unsplash_pictures = array(
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
        $unsplash_offset = 0;

        for ($c = 0; $c < 2; $c++) {

            // Categories
            $category = new Category();
            $category->setName(ucwords($faker->word));
            $category->setSlug($this->slugger->slug(strtolower($category->getName())));
            $manager->persist($category);
            $manager->flush();

            // Tricks
            for ($t = 0; $t < 5; $t++) {
                $trick = new Trick();

                $trick->setName(ucwords($faker->word))
                    ->setDescription($faker->text(150))
                    ->setCreatedAt(new \DateTime)
                    ->setSlug($this->slugger->slug(strtolower($trick->getName())))
                    ->setCategory($category);

                // Pictures
                shuffle($unsplash_pictures);

                for ($p = 0; $p < 2; $p++) {
                    $unsplash_offset++;
                    $unsplash_url = !empty($unsplash_pictures[$unsplash_offset]) ? $unsplash_pictures[$unsplash_offset] : $unsplash_pictures[0];

                    $file_name = $c . '_' . $t . '_' . $p . '.jpg';
                    $filename_path = $this->params->get('pictures_directory') . '/' . $file_name;
                    file_put_contents($filename_path . '', file_get_contents($unsplash_url));

                    $picture = new Picture;
                    $picture->setName($file_name);
                    $trick->addPicture($picture);
                }

                $manager->persist($trick);
            }
        }

        $manager->flush();
    }
}

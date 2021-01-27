<?php

namespace App\DataFixtures;

use App\Entity\Picture;
use App\DataFixtures\TrickFixtures;
use Doctrine\Persistence\ObjectManager;
use App\Service\Picture as PictureService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PictureFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{

    protected $pictureService;

    public function __construct(
        ParameterBagInterface $params,
        PictureService $pictureService
    ) {
        $this->params = $params;
        $this->pictureService = $pictureService;
    }

    public function load(ObjectManager $manager)
    {
        $tricks = [];
        for ($i = 0; $i < TrickFixtures::TRICK_NB; $i++) {
            $tricks[] = $this->getReference('trick_' . $i);
        }

        $urls = array(
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
        $trick_nb = TrickFixtures::TRICK_NB * 2;

        for ($i = 0; $i < $trick_nb; $i++) {
            shuffle($tricks);
            shuffle($urls);
            $fileName = $i . '.jpg';
            $filePath = $this->params->get('uploads_trick_path') . '/' . $fileName;
            $url = !empty($urls[$i]) ? $urls[$i] : $urls[0];
            if (file_put_contents($filePath, file_get_contents($url))) {
                $this->pictureService->crop($filePath, 1.5);
                $picture = new Picture;
                $picture->setName($fileName);
                $picture->setTrick($tricks[0]);
                $manager->persist($picture);
            }
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function getDependencies()
    {
        return array(
            InitFixtures::class,
            TrickFixtures::class,
        );
    }
}

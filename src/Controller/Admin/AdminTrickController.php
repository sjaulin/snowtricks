<?php

namespace App\Controller\Admin;

use App\Entity\Picture;
use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Repository\PictureRepository;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(
        TrickRepository $repository,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ) {
        $this->repository = $repository;
        $this->em = $em;
        $this->slugger = $slugger;
    }

    /**
     * @Route("/admin", name="admin.trick.index")
     */
    public function index()
    {
        $tricks = $this->repository->findAll();
        return $this->render('admin/trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }
}

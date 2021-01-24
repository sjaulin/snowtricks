<?php

namespace App\Controller\Admin;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(
        TrickRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index()
    {
        $tricks = $this->repository->findAll();
        return $this->render('admin/trick/index.html.twig', [
            'tricks' => $tricks
        ]);
    }
}

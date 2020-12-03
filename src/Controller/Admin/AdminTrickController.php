<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(TrickRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/admin", name="admin.trick.index")
     * @return Response
     */
    public function index()
    {
        $tricks = $this->repository->findAll();
        return $this->render('admin/trick/index.html.twig', compact('tricks'));
    }

    /**
     * @Route("/admin/{id}", name="admin.trick.edit")
     * @return void
     */
    public function edit(Trick $trick)
    {
        $form = $this->createForm(TrickType::class, $trick);

        return $this->render('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }
}

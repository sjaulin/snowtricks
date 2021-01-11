<?php

namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminTrickController extends AbstractController
{

    /**
     * @var TrickRepository
     */
    private $repository;

    public function __construct(TrickRepository $repository, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->em = $em;
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

    /**
     * @Route("/admin/trick/edit/{id}", name="admin.trick.edit")
     */
    public function edit(Trick $trick, Request $request)
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            return $this->redirectToRoute('admin.trick.index');
        }

        return $this->render('admin/trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/trick/create", name="admin.trick.create")
     */
    public function create(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($trick);
            $this->em->flush();
            return $this->redirectToRoute('admin.trick.index');
        }

        return $this->render('admin/trick/create.html.twig', [
            'trick' => $trick,
            'formView' => $form->createView()
        ]);
    }
}

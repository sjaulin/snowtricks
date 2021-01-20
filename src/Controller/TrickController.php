<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CategoryRepository;
use App\Repository\TrickRepository;
use App\Entity\Trick;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function home(TrickRepository $repository): Response
    {

        $tricks = $repository->findAll();

        return new Response($this->render('home.html.twig', [
            'tricks' => $tricks
        ]));
    }

    /**
     * @Route("/{slug}", name="trick.list")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {

        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException('category-not-found');
        }

        $tricks = $category->getTricks();

        return $this->render('trick/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="trick.show")
     */
    public function show($slug, Request $request, TrickRepository $trickRepository): Response
    {

        $trick = $trickRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$trick) {
            throw $this->createNotFoundException('trick-not-found');
        }

        $comment = new Comment;
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTrick($trick);
            dd($comment);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }
}

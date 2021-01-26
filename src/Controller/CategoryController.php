<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    public function __construct(
        SluggerInterface $slugger
    ) {
        $this->slugger = $slugger;
    }

    /**
     * @Route("/admin/category", name="category_admin", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/admin.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/category/new", name="category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug($this->slugger->slug(strtolower($category->getName())));
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_admin');
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}/edit", name="category_edit", methods={"GET","POST"})
     */
    public function edit($id, Request $request, Category $category, CategoryRepository $categoryRepository): Response
    {
        $othersCategories = $categoryRepository->findByDifferentId($id);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug($this->slugger->slug(strtolower($category->getName())));
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('category_admin');
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'others_categories' => $othersCategories,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/category/{id}/delete", name="category_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Category $category,
        CategoryRepository $categoryRepository
    ): Response {

        if ($this->isCsrfTokenValid(
            'delete' . $category->getId(),
            $request->request->get('_token')
        )) {
            $entityManager = $this->getDoctrine()->getManager();
            $categoryTarget = $categoryRepository->find($request->get('category_target'));

            if ($category) {
                $tricksToMove = $category->getTricks();
                foreach ($tricksToMove as $trickToMove) {
                    // @var Trick
                    $trickToMove->setCategory($categoryTarget);
                }
            }
            $entityManager->flush();

            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_admin');
    }
}

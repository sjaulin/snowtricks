<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends abstractController
{

    /**
     * @Route("/", name="home", methods={"GET"})
     */
    public function index(TrickRepository $repository): Response
    {
        $tricks = $repository->findAll();
        return new Response($this->render('pages/home.html.twig', [
            'tricks' => $tricks
        ]));
    }
}

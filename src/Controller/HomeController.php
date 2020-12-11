<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;

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

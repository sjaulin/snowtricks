<?php
namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends abstractController
{

    /**
    * @Route("/", name="home")
    * @param TrickRepository $repository
    * @return Response
    */
    public function index(TrickRepository $repository): Response
    {
        $tricks = $repository->findAll();
        return new Response($this->render('pages/home.html.twig', [
            'tricks' => $tricks
        ]));
    }
}

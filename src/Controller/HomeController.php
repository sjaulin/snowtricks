<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends abstractController
{

    /**
    * @Route("/", name="home")
    *
    * @return Response
    */
    public function index(): Response
    {
        return new Response($this->render('pages/home.html.twig'));
    }
}

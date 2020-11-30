<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TricksController extends AbstractController
{

    /**
     * @Route("/tricks", name="tricks.index")
     *
     * @return Response
     */
    public function index(): Response
    {
        return new Response($this->render(
            'pages/tricks/index.html.twig',
            [
                'current_path' => 'tricks'
            ]
        ));
    }
}

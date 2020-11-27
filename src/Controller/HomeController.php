<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeController
{

    /**
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    
    public function index(): Response
    {
        return new Response($this->twig->render('pages/home.html.twig'));
    }
}

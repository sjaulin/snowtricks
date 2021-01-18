<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="security_login")
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $lastUser = $utils->getLastUsername();
        $form = $this->createForm(LoginType::class, ['email' => $lastUser['email']]);

        $error = $utils->getLastAuthenticationError(); // Récupère les erreurs via attributs ou session
        return $this->render('security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }
}

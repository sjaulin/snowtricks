<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Security;

class LoginFormAuthenticator extends AbstractGuardAuthenticator
{

    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // Quels conditions l'authenticator contrôle la route.
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'security_login'
            && $request->isMethod('POST');
    }

    // Retourne les informations nécessaires à l'authentification.
    public function getCredentials(Request $request)
    {
        return $request->request->get('login'); // array avec 3 éléments
    }

    // Vérifiee la présence de l'utilisateur via le provider
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            return $userProvider->loadUserByUsername($credentials['email']);
        } catch (UsernameNotFoundException $e) {
            throw new AuthenticationException('Cet utilisateur n\'exite pas');
        }
    }

    // Vérifie que le mot de passe fournit est le même que celui du provider.
    public function checkCredentials($credentials, UserInterface $user)
    {
        $isvalid = $this->encoder->isPasswordValid($user, $credentials['password']);
        if (!$isvalid) {
            throw new AuthenticationException('Mot de passe incorrect');
        }

        return $isvalid;
    }

    // En cas d'erreur sur l'une des étapes précédentes
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // On enregistre l'exception levé dans les attributs pour la retrouver dans le getLastAuthenticationError du securityController.
        $request->attributes->set(Security::AUTHENTICATION_ERROR, $exception);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        // TODO message de success
        return new RedirectResponse("/");
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // todo
    }

    public function supportsRememberMe()
    {
        // todo
    }
}

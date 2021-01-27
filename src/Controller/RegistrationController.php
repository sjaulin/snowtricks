<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Avatar;
use App\Form\EmailConfirmationFormType;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use App\Service\Picture as PictureService;

class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $pictureService;

    public function __construct(EmailVerifier $emailVerifier, PictureService $pictureService)
    {
        $this->emailVerifier = $emailVerifier;
        $this->pictureService = $pictureService;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // TODO Move to service or private function in this class ?
            // Save avatar.
            $avatar = $form->get('avatar')->getData();
            $avatarDirectory = $this->getParameter('uploads_user_path');
            $filename = md5(uniqid()) . '.' . $avatar->guessExtension(); // Require php.ini : extension=fileinfo
            $avatar->move(
                $avatarDirectory,
                $filename
            );
            $this->pictureService->crop($avatarDirectory . '/' . $filename, 1);
            $this->pictureService->scale($avatarDirectory . '/' . $filename, 200, 200);

            // Create Avatar entity
            $avatar = new Avatar;
            $avatar->setName($filename);

            // Attach to user
            $user->setAvatar($avatar);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('stephanejaulin+snowtricks@gmail.com', 'Sowtricks'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Resquest a new link to verify user email
     *
     * @Route("/verify/mail/request", name="app_verify_email_request")
     */
    public function verifyUserEmailRequest(Request $request, UserRepository $userRepository): Response
    {

        $user = new User();
        $form = $this->get('form.factory')->createNamed('', EmailConfirmationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('stephanejaulin+snowtricks@gmail.com', 'Sowtricks'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            return $this->redirectToRoute('security_login');
        }

        return $this->render('registration/verify_email_request.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * The email to confirm the email points to this route.
     *
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('home');
    }
}

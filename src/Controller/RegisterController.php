<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="auth_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $hasher, TokenGeneratorInterface $tokenGenerator, Mailer $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setTokenVerification($tokenGenerator->generateToken());
            $user->setPassword(
                $hasher->hashPassword($user, $form->get('password')->getData())
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $mailer->sendEmailVerification($user->getEmail(), $user->getTokenVerification());

            $this->addFlash('info', 'Veuillez cliquer sur le lien de confirmation envoyé par email avant de vous connecter.');

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('authentication/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify-email/{tokenVerification}", name="auth_verify_email")
     */
    public function verifyUserEmail(User $user): Response
    {
            $user->setTokenVerification(null);
            $user->setIsVerified(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte actif !');

            return $this->redirectToRoute('auth_login');
    }
}

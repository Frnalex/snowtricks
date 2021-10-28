<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\LoginType;
use App\Form\RepeatedPasswordType;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    /**
     * @Route("/login", name="auth_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);

        return $this->render('authentication/login.html.twig', [
            'loginForm' => $form->createView(),
            'error' => $utils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="auth_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/forgot-password", name="auth_forgot_password")
     */
    public function forgotPassword(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, Mailer $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userRepository->findOneBy([
                'email' => $form['email']->getData(),
            ]);

            if (!$user) {
                $this->addFlash('error', "Aucun utilisateur n'est enregisté avec cette adresse");

                return $this->redirectToRoute('auth_forgot_password');
            }

            $user->setTokenForgotPassword($tokenGenerator->generateToken());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $mailer->sendForgotPasswordEmail($user->getEmail(), $user->getTokenForgotPassword());

            $this->addFlash('success', 'Un email vous a été envoyé pour redéfinir votre mot de passe');

            return $this->redirectToRoute('auth_forgot_password');
        }

        return $this->render('authentication/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{tokenForgotPassword}", name="auth_reset_password")
     */
    public function resetPassword(User $user, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(RepeatedPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $hasher->hashPassword($user, $form->getData())
            );
            $user->setTokenForgotPassword(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Le mot de passe a bien été réinitialisé');

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('authentication/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

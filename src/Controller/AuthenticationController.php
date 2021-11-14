<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\LoginType;
use App\Form\RepeatedPasswordType;
use App\Handler\UserHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function forgotPassword(Request $request, UserHandler $userHandler)
    {
        $form = $this->createForm(ForgotPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userHandler->forgotPassword($form['email']->getData());
        }

        return $this->render('authentication/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset-password/{tokenForgotPassword}", name="auth_reset_password")
     */
    public function resetPassword(User $user, Request $request, UserHandler $userHandler): Response
    {
        $form = $this->createForm(RepeatedPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userHandler->resetPassword($user, $form->getData());
        }

        return $this->render('authentication/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

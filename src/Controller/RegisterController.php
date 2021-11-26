<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Handler\UserHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="auth_register")
     */
    public function register(Request $request, UserHandlerInterface $userHandler): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userHandler->register($user, $form->get('password')->getData());

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('authentication/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify-email/{tokenVerification}", name="auth_verify_email")
     */
    public function verifyUserEmail(User $user, UserHandlerInterface $userHandler)
    {
        $userHandler->verifyEmail($user);

        return $this->redirectToRoute('auth_login');
    }
}

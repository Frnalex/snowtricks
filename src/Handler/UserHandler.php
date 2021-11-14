<?php

namespace App\Handler;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserHandler
{
    private $fileUploader;
    private $em;
    private $userRepository;
    private $tokenGenerator;
    private $mailer;
    private $flashBag;
    private $hasher;
    private $urlGenerator;

    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        Mailer $mailer,
        FlashBagInterface $flashBag,
        UserPasswordHasherInterface $hasher,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->fileUploader = $fileUploader;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->flashBag = $flashBag;
        $this->hasher = $hasher;
        $this->urlGenerator = $urlGenerator;
    }

    public function profilePicture($user, $form): RedirectResponse
    {
        /** @var Image */
        $image = $form->getData();

        if (null !== $image->getFile()) {
            $path = $this->fileUploader->upload($image->getFile());
            $image->setAlt('Photo de profil');
            $image->setName($path);
            $user->setProfilePicture($image);
        }

        $this->em->flush();

        $response = new RedirectResponse($this->urlGenerator->generate('user_profile'));

        return $response->send();
    }

    public function forgotPassword($email)
    {
        $user = $this->userRepository->findOneBy([
            'email' => $email,
        ]);

        if (!$user) {
            $this->flashBag->add('danger', "Aucun utilisateur n'est enregisté avec cette adresse");

            return;
        }

        $user->setTokenForgotPassword($this->tokenGenerator->generateToken());

        $this->em->persist($user);
        $this->em->flush();

        $this->mailer->sendForgotPasswordEmail($user->getEmail(), $user->getTokenForgotPassword());

        $this->flashBag->add('success', 'Un email vous a été envoyé pour redéfinir votre mot de passe');
    }

    public function resetPassword(User $user, $password): RedirectResponse
    {
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setTokenForgotPassword(null);

        $this->em->persist($user);
        $this->em->flush();

        $this->flashBag->add('success', 'Le mot de passe a bien été réinitialisé');

        $response = new RedirectResponse($this->urlGenerator->generate('auth_login'));

        return $response->send();
    }

    public function register(User $user, $password): RedirectResponse
    {
        $user->setTokenVerification($this->tokenGenerator->generateToken());
        $user->setPassword(
            $this->hasher->hashPassword($user, $password)
        );

        $this->em->persist($user);
        $this->em->flush();

        $this->mailer->sendEmailVerification($user->getEmail(), $user->getTokenVerification());

        $this->flashBag->add('info', 'Veuillez cliquer sur le lien de confirmation envoyé par email avant de vous connecter.');

        $response = new RedirectResponse($this->urlGenerator->generate('auth_login'));

        return $response->send();
    }

    public function verifyEmail(User $user): RedirectResponse
    {
        $user->setTokenVerification(null);
        $user->setIsVerified(true);
        $this->em->persist($user);
        $this->em->flush();

        $this->flashBag->add('success', 'Compte actif !');

        $response = new RedirectResponse($this->urlGenerator->generate('auth_login'));

        return $response->send();
    }
}

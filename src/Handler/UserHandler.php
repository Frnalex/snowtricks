<?php

namespace App\Handler;

use App\Entity\Image;
use App\Entity\User;
use App\Service\FileUploader;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class UserHandler implements UserHandlerInterface
{
    private $em;
    private $flashBag;
    private $fileUploader;
    private $tokenGenerator;
    private $mailer;
    private $hasher;

    public function __construct(
        EntityManagerInterface $em,
        FlashBagInterface $flashBag,
        FileUploader $fileUploader,
        TokenGeneratorInterface $tokenGenerator,
        Mailer $mailer,
        UserPasswordHasherInterface $hasher
    ) {
        $this->em = $em;
        $this->flashBag = $flashBag;
        $this->fileUploader = $fileUploader;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->hasher = $hasher;
    }

    public function changeProfilePicture(User $user, Image $image)
    {
        if (null !== $image->getFile()) {
            $path = $this->fileUploader->upload($image->getFile());
            $image->setAlt('Photo de profil');
            $image->setName($path);
            $user->setProfilePicture($image);
        }

        $this->em->flush();
    }

    public function register(User $user, $password)
    {
        $user->setTokenVerification($this->tokenGenerator->generateToken());
        $user->setPassword(
            $this->hasher->hashPassword($user, $password)
        );

        $this->em->persist($user);
        $this->em->flush();

        $this->mailer->sendEmailVerification($user->getEmail(), $user->getTokenVerification());

        $this->flashBag->add('info', 'Veuillez cliquer sur le lien de confirmation envoyé par email avant de vous connecter.');
    }

    public function verifyEmail(User $user)
    {
        $user->setTokenVerification(null);
        $user->setIsVerified(true);
        $this->em->persist($user);
        $this->em->flush();

        $this->flashBag->add('success', 'Compte actif !');
    }

    public function forgotPassword(User $user)
    {
        $user->setTokenForgotPassword($this->tokenGenerator->generateToken());

        $this->em->persist($user);
        $this->em->flush();

        $this->mailer->sendForgotPasswordEmail($user->getEmail(), $user->getTokenForgotPassword());

        $this->flashBag->add('success', 'Un email vous a été envoyé pour redéfinir votre mot de passe');
    }

    public function resetPassword(User $user, $password)
    {
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $user->setTokenForgotPassword(null);

        $this->em->persist($user);
        $this->em->flush();

        $this->flashBag->add('success', 'Le mot de passe a bien été réinitialisé');
    }
}

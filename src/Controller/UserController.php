<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\ImageType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="user_profile")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request, FileUploader $fileUploader, EntityManagerInterface $em)
    {
        $form = $this->createForm(ImageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            /** @var Image */
            $image = $form->getData();

            if (null !== $image->getFile()) {
                $path = $fileUploader->upload($image->getFile());
                $image->setAlt('Photo de profil');
                $image->setName($path);
                $user->setProfilePicture($image);
            }

            $em->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('profile.html.twig', [
            'imageForm' => $form->createView(),
        ]);
    }
}

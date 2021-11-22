<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ImageType;
use App\Handler\UserHandlerInterface;
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
    public function profile(Request $request, UserHandlerInterface $userHandler)
    {
        $form = $this->createForm(ImageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userHandler->changeProfilePicture($this->getUser(), $form->getData());
        }

        return $this->render('profile.html.twig', [
            'imageForm' => $form->createView(),
        ]);
    }
}

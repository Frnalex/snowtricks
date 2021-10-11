<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepage(TrickRepository $trickRepository)
    {
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}

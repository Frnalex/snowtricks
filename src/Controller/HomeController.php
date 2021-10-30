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
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], 6);
        $total = $trickRepository->count([]);

        return $this->render('home.html.twig', [
            'tricks' => $tricks,
            'total' => $total
        ]);
    }

    /**
     * @Route("/{offset}", name="loadMoreTricks", requirements={"offset": "\d+"})
     */
    public function loadMoreTricks(TrickRepository $trickRepository, $offset = 6)
    {
        $tricks = $trickRepository->findBy([], ['createdAt' => 'DESC'], 6, $offset);

        return $this->render('trickList.html.twig', [
            'tricks' => $tricks,
        ]);
    }
}

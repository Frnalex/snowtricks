<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_show")
     *
     * @param string $slug
     */
    public function show($slug, TrickRepository $trickRepository)
    {
        $trick = $trickRepository->findOneBy([
            'slug' => $slug,
        ]);

        if (!$trick) {
            throw $this->createNotFoundException("Le trick demandé n'existe pas");
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }
}
<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_show")
     */
    public function show(Trick $trick)
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * @Route("/trick/add", name="trick_add")
     */
    public function add(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $form = $this->createForm(TrickType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setSlug($slugger->slug($trick->getName())->lower());

            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/add.html.twig', [
            'trickForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="trick_edit")
     */
    public function edit(Trick $trick, Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $trick->setUpdatedAt(new \DateTime());
            $em->flush();

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form->createView(),
        ]);
    }
}

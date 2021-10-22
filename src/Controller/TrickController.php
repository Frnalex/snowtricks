<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/{slug}", name="trick_show")
     */
    public function show(Trick $trick, Request $request)
    {
        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (null === $this->getUser()) {
                return $this->redirectToRoute('auth_login');
            }

            $comment = $form->getData();
            $comment->setTrick($trick);
            $comment->setUser($this->getUser());

            $this->em->persist($comment);
            $this->em->flush();

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/trick/add", name="trick_add")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, SluggerInterface $slugger, FileUploader $fileUploader)
    {
        $form = $this->createForm(TrickType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setSlug($slugger->slug($trick->getName())->lower());

            /** @var Image $image */
            foreach ($trick->getImages() as $image) {
                $path = $fileUploader->upload($image->getFile());
                $image->setName($path);
            }

            $this->em->persist($trick);
            $this->em->flush();

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
     * @IsGranted("ROLE_USER")
     */
    public function edit(Trick $trick, Request $request, SluggerInterface $slugger, FileUploader $fileUploader)
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug($slugger->slug($trick->getName())->lower());
            $trick->setUpdatedAt(new \DateTime());

            /** @var Image $image */
            foreach ($trick->getImages() as $image) {
                if (null === $image->getName()) {
                    $path = $fileUploader->upload($image->getFile());
                    $image->setName($path);
                }
            }

            $this->em->flush();

            return $this->redirectToRoute('trick_show', [
                'slug' => $trick->getSlug(),
            ]);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}/delete", name="trick_delete")
     * @IsGranted("ROLE_USER")
     */
    public function delete(Trick $trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->addFlash('trick_delete_success', 'Le trick a bien été supprimé de la base de donnée');

        return $this->redirectToRoute('homepage');
    }
}

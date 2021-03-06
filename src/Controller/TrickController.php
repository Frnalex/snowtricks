<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Handler\TrickHandlerInterface;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    /**
     * @Route("/{slug}", name="trick_show", priority=-1)
     */
    public function show(Trick $trick, Request $request, CommentRepository $commentRepository, TrickHandlerInterface $trickHandler)
    {
        $page = max(0, $request->query->getInt('page', 1));
        $paginator = $commentRepository->getCommentPaginator($trick, $page);

        $form = $this->createForm(CommentType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            null === $this->getUser() && $this->redirectToRoute('auth_login');

            $trickHandler->addComment($this->getUser(), $form->getData(), $trick);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $paginator,
            'page' => $page,
            'commentForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/trick/add", name="trick_add")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, TrickHandlerInterface $trickHandler)
    {
        $form = $this->createForm(TrickType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $trickHandler->add($form->getData());

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
    public function edit(Trick $trick, Request $request, TrickHandlerInterface $trickHandler)
    {
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $trickHandler->edit($trick);

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
    public function delete(Trick $trick, TrickHandlerInterface $trickHandler)
    {
        $trickHandler->delete($trick);

        return $this->redirectToRoute('homepage');
    }
}

<?php

namespace App\Handler;

use App\Entity\Trick;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickHandler extends AbstractHandler
{
    private $slugger;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        FlashBagInterface $flashBag,
        SluggerInterface $slugger
    ) {
        parent::__construct($urlGenerator, $em, $fileUploader, $flashBag);
        $this->fileUploader = $fileUploader;
        $this->slugger = $slugger;
        $this->flashBag = $flashBag;
    }

    public function add($trick): RedirectResponse
    {
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $this->uploadImages($trick);

        $this->em->persist($trick);
        $this->em->flush();

        return $this->redirectTo('trick_show', [
            'slug' => $trick->getSlug(),
        ]);
    }

    public function edit($trick): RedirectResponse
    {
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $trick->setUpdatedAt(new \DateTime());

        $this->uploadImages($trick);

        $this->em->flush();

        return $this->redirectTo('trick_show', [
            'slug' => $trick->getSlug(),
        ]);
    }

    public function delete($trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->flashBag->add('success', 'Le trick a bien été supprimé de la base de donnée');

        return $this->redirectTo('homepage');
    }

    public function addComment($user, $comment, $trick): RedirectResponse
    {
        if (null === $user) {
            return $this->redirectTo('auth_login');
        }

        $comment->setTrick($trick);
        $comment->setUser($user);

        $this->em->persist($comment);
        $this->em->flush();

        return $this->redirectTo('trick_show', [
            'slug' => $trick->getSlug(),
        ]);
    }

    private function uploadImages(Trick $trick)
    {
        /** @var Image */
        $mainImage = $trick->getMainImage();
        if (null !== $mainImage->getFile()) {
            $path = $this->fileUploader->upload($mainImage->getFile());
            $mainImage->setName($path);
        }

        /** @var Image $image */
        foreach ($trick->getImages() as $image) {
            if (null !== $image->getFile()) {
                $path = $this->fileUploader->upload($image->getFile());
                $image->setName($path);
            }
        }
    }
}

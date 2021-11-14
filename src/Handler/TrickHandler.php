<?php

namespace App\Handler;

use App\Entity\Trick;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickHandler
{
    private $fileUploader;
    private $em;
    private $slugger;
    private $urlGenerator;
    private $flashBag;

    public function __construct(
        FileUploader $fileUploader,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag
    ) {
        $this->fileUploader = $fileUploader;
        $this->em = $em;
        $this->slugger = $slugger;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
    }

    public function add($trick): RedirectResponse
    {
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $this->uploadImages($trick);

        $this->em->persist($trick);
        $this->em->flush();

        $response = new RedirectResponse($this->urlGenerator->generate('trick_show', [
            'slug' => $trick->getSlug(),
        ]));

        return $response->send();
    }

    public function edit($trick): RedirectResponse
    {
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $trick->setUpdatedAt(new \DateTime());

        $this->uploadImages($trick);

        $this->em->flush();

        $response = new RedirectResponse($this->urlGenerator->generate('trick_show', [
            'slug' => $trick->getSlug(),
        ]));

        return $response->send();
    }

    public function delete($trick)
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->flashBag->add('success', 'Le trick a bien été supprimé de la base de donnée');

        $response = new RedirectResponse($this->urlGenerator->generate('homepage'));

        return $response->send();
    }

    public function addComment($user, $comment, $trick): RedirectResponse
    {
        if (null === $user) {
            $response = new RedirectResponse($this->urlGenerator->generate('auth_login'));

            return $response->send();
        }

        $comment->setTrick($trick);
        $comment->setUser($user);

        $this->em->persist($comment);
        $this->em->flush();

        $response = new RedirectResponse($this->urlGenerator->generate('trick_show', [
            'slug' => $trick->getSlug(),
        ]));

        return $response->send();
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

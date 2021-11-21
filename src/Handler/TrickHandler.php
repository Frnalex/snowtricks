<?php

namespace App\Handler;

use App\Entity\Trick;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class TrickHandler implements TrickHandlerInterface
{
    private $em;
    private $flashBag;
    private $fileUploader;
    private $slugger;

    public function __construct(
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        FlashBagInterface $flashBag,
        SluggerInterface $slugger
    ) {
        $this->em = $em;
        $this->flashBag = $flashBag;
        $this->fileUploader = $fileUploader;
        $this->slugger = $slugger;
    }

    public function add($trick): Trick
    {
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $this->uploadImages($trick);

        $this->em->persist($trick);
        $this->em->flush();

        return $trick;
    }

    public function edit($trick): Trick
    {
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $trick->setUpdatedAt(new \DateTime());

        $this->uploadImages($trick);

        $this->em->flush();

        return $trick;
    }

    public function delete($trick): void
    {
        $this->em->remove($trick);
        $this->em->flush();

        $this->flashBag->add('success', 'Le trick a bien été supprimé de la base de donnée');
    }

    public function addComment($user, $comment, $trick): void
    {
        $comment->setTrick($trick);
        $comment->setUser($user);

        $this->em->persist($comment);
        $this->em->flush();
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

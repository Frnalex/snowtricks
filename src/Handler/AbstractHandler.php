<?php

namespace App\Handler;

use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbstractHandler
{
    protected $urlGenerator;
    protected $em;
    protected $fileUploader;
    protected $flashBag;

    protected function __construct(
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        FileUploader $fileUploader,
        FlashBagInterface $flashBag
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->fileUploader = $fileUploader;
        $this->flashBag = $flashBag;
    }

    protected function redirectTo(string $name, array $parameters = []): RedirectResponse
    {
        $response = new RedirectResponse($this->urlGenerator->generate($name, $parameters));

        return $response->send();
    }
}

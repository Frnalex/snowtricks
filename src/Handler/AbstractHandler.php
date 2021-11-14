<?php

namespace App\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AbstractHandler
{
    public $urlGenerator;
    protected $em;

    public function __construct(UrlGeneratorInterface $urlGenerator, EntityManagerInterface $em)
    {
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
    }

    public function redirectTo(string $name, array $parameters = []): RedirectResponse
    {
        $response = new RedirectResponse($this->urlGenerator->generate($name, $parameters));

        return $response->send();
    }
}

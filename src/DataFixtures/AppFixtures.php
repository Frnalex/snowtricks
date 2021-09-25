<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 20; ++$i) {
            $trick = new Trick();
            $trick
                ->setName("Trick numéro {$i}")
                ->setDescription('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iusto, explicabo!')
                ->setSlug(strtolower($this->slugger->slug($trick->getName())))
                ->setCreatedAt(new \DateTime())
            ;

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
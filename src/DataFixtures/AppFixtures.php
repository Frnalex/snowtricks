<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
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
        for ($c = 1; $c <= 3; ++$c) {
            $category = new Category();
            $category
                ->setName("Catégorie {$c}")
                ->setSlug(strtolower($this->slugger->slug($category->getName())))
            ;
            $manager->persist($category);

            for ($t = 1; $t <= mt_rand(3, 10); ++$t) {
                $trick = new Trick();
                $trick
                    ->setName("Trick numéro {$t} - Catégorie {$c}")
                    ->setDescription('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iusto, explicabo!')
                    ->setSlug(strtolower($this->slugger->slug($trick->getName())))
                    ->setCreatedAt(new \DateTime())
                    ->setCategory($category)
                ;
                $manager->persist($trick);

                for ($i = 1; $i <= mt_rand(2, 5); ++$i) {
                    $comment = new Comment();
                    $comment
                        ->setContent("Commentaire {$i} Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero praesentium, voluptatum expedita incidunt deserunt nisi assumenda quos tenetur fugit architecto.")
                        ->setCreatedAt(new \DateTime())
                        ->setTrick($trick)
                    ;
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
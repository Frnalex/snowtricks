<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $hasher;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        for ($c = 1; $c <= 3; ++$c) {
            $category = new Category();
            $category
                ->setName("Catégorie {$c}")
                ->setSlug($this->slugger->slug($category->getName())->lower())
            ;
            $manager->persist($category);

            for ($t = 1; $t <= mt_rand(3, 10); ++$t) {
                $trick = new Trick();
                $trick
                    ->setName("Trick numéro {$t} - Catégorie {$c}")
                    ->setDescription('Lorem ipsum dolor sit amet consectetur, adipisicing elit. Iusto, explicabo!')
                    ->setSlug($this->slugger->slug($trick->getName())->lower())
                    ->setCategory($category)
                ;
                $manager->persist($trick);

                for ($i = 1; $i <= mt_rand(2, 5); ++$i) {
                    $comment = new Comment();
                    $comment
                        ->setContent("Commentaire {$i} Lorem ipsum dolor sit amet consectetur adipisicing elit. Libero praesentium, voluptatum expedita incidunt deserunt nisi assumenda quos tenetur fugit architecto.")
                        ->setTrick($trick)
                    ;
                    $manager->persist($comment);
                }
            }
        }

        for ($u = 1; $u <= 5; ++$u) {
            $user = new User();

            $hash = $this->hasher->hashPassword($user, 'password');

            $user
                ->setEmail("user{$u}@gmail.com")
                ->setPassword($hash)
                ->setUsername("user{$u}")
                ->setIsVerified(true)
            ;

            $manager->persist($user);
        }

        $manager->flush();
    }
}

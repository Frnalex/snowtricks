<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $hasher;
    protected $categories;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
        $this->categories = [
            'Grab' => [
                'Mute' => 'saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
                'Melancholie' => 'saisie de la carre backside de la planche, entre les deux pieds, avec la main avant',
                'Indy' => 'saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière',
                'Stalefish' => 'saisie de la carre backside de la planche entre les deux pieds avec la main arrière',
                'Tail grab' => 'saisie de la partie arrière de la planche, avec la main arrière',
                'Nose grab' => 'saisie de la partie avant de la planche, avec la main avant',
                'Japan air' => "saisie de l'avant de la planche, avec la main avant, du côté de la carre frontside",
                'Seat belt' => "saisie du carre frontside à l'arrière avec la main avant",
                'Truck driver' => 'saisie du carre avant et carre arrière avec chaque main (comme tenir un volant de voiture)',
            ],
            'Rotation' => [
                '180' => "demi-tour, soit 180 degrés d'angle",
                '360' => 'trois six pour un tour complet',
                '540' => 'cinq quatre pour un tour et demi',
                '720' => 'sept deux pour deux tours complets',
                '900' => 'deux tours et demi',
                '1080' => 'trois tours',
            ],
            'Flip' => [
                'Front flip' => 'Rotation en avant',
                'Back flip' => 'Rotation en arrière',
            ],
        ];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $users = [];

        for ($u = 1; $u <= 5; ++$u) {
            $user = new User();

            $hash = $this->hasher->hashPassword($user, 'password');

            $user
                ->setEmail("user{$u}@gmail.com")
                ->setPassword($hash)
                ->setUsername($faker->userName())
                ->setIsVerified(true)
            ;

            $users[] = $user;

            $manager->persist($user);
        }

        foreach ($this->categories as $c => $listTricks) {
            $category = new Category();
            $category
                ->setName($c)
                ->setSlug($this->slugger->slug($category->getName())->lower())
            ;
            $manager->persist($category);

            foreach ($listTricks as $t => $description) {
                $trick = new Trick();
                $trick
                    ->setName($t)
                    ->setDescription($description)
                    ->setSlug($this->slugger->slug($trick->getName())->lower())
                    ->setCategory($category)
                ;
                $manager->persist($trick);

                for ($i = 1; $i <= mt_rand(2, 5); ++$i) {
                    $image = new Image();
                    $image->setName('default-trick.png');
                    $image->setTrick($trick);
                    $manager->persist($image);
                }

                for ($com = 1; $com <= mt_rand(16, 32); ++$com) {
                    $comment = new Comment();
                    $comment
                        ->setContent($faker->paragraph())
                        ->setTrick($trick)
                        ->setUser($faker->randomElement($users))
                    ;
                    $manager->persist($comment);
                }

                for ($v = 1; $v < mt_rand(2, 3); ++$v) {
                    $video = new Video();
                    $video
                        ->setUrl('https://www.youtube.com/embed/t705_V-RDcQ')
                        ->setTrick($trick)
                    ;
                    $manager->persist($video);
                }
            }
        }

        $manager->flush();
    }
}

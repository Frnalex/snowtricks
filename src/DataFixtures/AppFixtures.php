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
    public const DATA = [
        'Grab' => [
            'Mute' => [
                'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.',
                'image' => 'mute.jpg',
            ],
            'Japan air' => [
                'description' => "Saisie de l'avant de la planche, avec la main avant, du côté de la carre frontside.",
                'image' => 'japan-air.jpg',
            ],
            'Indy' => [
                'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
                'image' => 'indy.jpg',
            ],
            'Stalefish' => [
                'description' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière.',
                'image' => 'stalefish.jpg',
            ],
            'Tail grab' => [
                'description' => 'Saisie de la partie arrière de la planche, avec la main arrière.',
                'image' => 'tail-grab.jpg',
            ],
        ],
        'Rotation' => [
            '360' => [
                'description' => 'Trois six pour un tour complet.',
                'image' => '360.jpg',
            ],
            '1080' => [
                'description' => 'Trois tours.',
                'image' => '1080.jpg',
            ],
        ],
        'Flip' => [
            'Front flip' => [
                'description' => 'Rotation en avant.',
                'image' => 'front-flip.jpg',
            ],
            'Back flip' => [
                'description' => 'Rotation en arrière.',
                'image' => 'back-flip.jpg',
            ],
        ],
    ];

    protected $slugger;
    protected $hasher;
    protected $categories;

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
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

        foreach (self::DATA as $c => $listTricks) {
            $category = new Category();
            $category
                ->setName($c)
                ->setSlug($this->slugger->slug($category->getName())->lower())
            ;
            $manager->persist($category);

            foreach ($listTricks as $t => $details) {
                $trick = new Trick();

                $mainImage = new Image();
                $mainImage->setName($details['image']);

                $trick
                    ->setName($t)
                    ->setDescription($details['description'])
                    ->setSlug($this->slugger->slug($trick->getName())->lower())
                    ->setCategory($category)
                    ->setMainImage($mainImage)
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

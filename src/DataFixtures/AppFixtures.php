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

    public function __construct(SluggerInterface $slugger, UserPasswordHasherInterface $hasher)
    {
        $this->slugger = $slugger;
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        $categories = ['Grab', 'Rotation', 'Flip', 'Slide', 'Old school'];

        $users = [];

        for ($u = 1; $u <= 5; ++$u) {
            $user = new User();

            $hash = $this->hasher->hashPassword($user, 'password');

            $profilePicture = new Image();
            $profilePicture->setName('default-profile.png');

            $user
                ->setEmail("user{$u}@gmail.com")
                ->setPassword($hash)
                ->setUsername($faker->userName())
                ->setIsVerified(true)
                ->setProfilePicture($profilePicture)
            ;

            $users[] = $user;

            $manager->persist($user);
        }

        foreach ($categories as $c) {
            $category = new Category();
            $category
                ->setName($c)
                ->setSlug($this->slugger->slug($category->getName())->lower())
            ;
            $manager->persist($category);

            for ($t = 1; $t <= mt_rand(3, 10); ++$t) {
                $mainImage = new Image();
                $mainImage->setName('default-trick.png');

                $trick = new Trick();
                $trick
                    ->setName($faker->sentence(3))
                    ->setDescription($faker->paragraph())
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

                for ($co = 1; $co <= mt_rand(10, 15); ++$co) {
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

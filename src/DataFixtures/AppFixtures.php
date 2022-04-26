<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct( UserPasswordHasherInterface $encoder, SluggerInterface $slugger)
    {
        $this->encoder = $encoder;
        $this->slugger = $slugger;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User();

        $hash = $this->encoder->hashPassword($admin, "AdminAdmin");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setUsername("admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $newName = $faker->firstName(null);
            $name = strtolower(strtok($newName, ' '));

            $hash = $this->encoder->hashPassword($user, "password");

            $user->setUsername($name)
                ->setEmail("$name@gmail.com")
                ->setPassword($hash);

            $manager->persist($user);
        }

        for ($c = 0; $c < 5; $c++) {
            $category = new Category;
            $category->setName($faker->country())
                ->setSlug(strtolower($this->slugger->slug($category->getName())));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $post = new Post;
                $post->setTitle($faker->state())
                    ->setSlug(strtolower($this->slugger->slug($post->getTitle())))
                    ->setShortDescription($faker->text(100))
                    ->setContent($faker->realText(1200, 2))
                    ->setCreatedAt(new \DateTimeImmutable())
                    ->setPicture($faker->imageUrl(1300, 1300, true))
                    ->setCategory($category);

                $manager->persist($post);
            }
        }

        $manager->flush();
    }
}

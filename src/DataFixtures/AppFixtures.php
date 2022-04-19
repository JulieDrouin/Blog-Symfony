<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct( UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr-FR');

        $admin = new User();

        $hash = $this->encoder->hashPassword($admin, "AdminAdmin");

        $admin->setEmail("admin@gmail.com")
            ->setPassword($hash)
            ->setUsername("admin")
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        for ($u=0; $u < 5; $u++) {
            $user = new User();
            $newName = $faker->name();
            $name = strtolower(strtok($newName, ' '));

            $hash = $this->encoder->hashPassword($user, "password");

            $user->setUsername($name)
                ->setEmail("$name@gmail.com")
                ->setPassword($hash);
        $manager->persist($user);
        }

        $manager->flush();
    }
}

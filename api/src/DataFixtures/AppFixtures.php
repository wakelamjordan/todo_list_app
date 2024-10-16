<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Loader\ObjectLoader;

class AppFixtures extends Fixture
{
    // private ObjectManager $manager;
    // private UserPasswordHasherInterface $passwordHasher;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        // $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $this->addAcount($manager);

        $manager->flush();
    }

    private function addAcount(ObjectManager $manager): void
    {
        $user = new User;

        // $passwordHasher = new UserPasswordHasherInterface;

        $userData = [
            "email" => "jwakelams@gmail.com",
            "password" => $this->passwordHasher->hashPassword($user, "4321")
        ];

        $user
            ->setEmail($userData["email"])
            ->setPassword($userData["password"]);

        $manager->persist($user);
    }
}

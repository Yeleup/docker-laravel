<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('magzhan9292@gmail.com');
        $user->setRoles(['ROLE_ADMIN']);
        $password = $this->passwordHasher->hashPassword($user, '147896');
        $user->setPassword($password);
        $manager->persist($user);

        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setRoles(['ROLE_USER']);
            $password = $this->passwordHasher->hashPassword($user, '147896');
            $user->setPassword($password);
            $manager->persist($user);
        }
        $manager->flush();
    }
}

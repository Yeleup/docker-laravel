<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Organization;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru_RU');

        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 1; $i <= 200; $i++) {
            $customer = new Customer();
            $customer->setName($faker->firstName);
            $customer->setUser($faker->randomElement($users));
            $customer->setAddress($faker->address);
            $customer->setPhone($faker->phoneNumber);
            $manager->persist($customer);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrganizationFixtures::class,
            OperationFixtures::class,
            UserFixtures::class,
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Operation;
use App\Entity\Organization;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TransactionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru_RU');

        $users = $manager->getRepository(User::class)->findAll();
        $customers = $manager->getRepository(Customer::class)->findAll();
        $operations = $manager->getRepository(Operation::class)->findAll();

        for ($i = 1; $i <= 200; $i++) {
            $transaction = new Transaction();
            $transaction->setUser($faker->randomElement($users));
            $transaction->setCustomer($faker->randomElement($customers));
            $transaction->setOperation($faker->randomElement($operations));
            $transaction->setAmount($faker->numberBetween(1, 20) * 500);
            $manager->persist($transaction);
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            OrganizationFixtures::class,
            OperationFixtures::class,
            UserFixtures::class,
            CustomerFixtures::class,
        ];
    }
}

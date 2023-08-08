<?php

namespace App\DataFixtures;

use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OrganizationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('ru_RU');
        for ($i = 1; $i <= 10; $i++) {
            $organization = new Organization();
            $organization->setTitle($faker->company);
            $manager->persist($organization);
        }
        $manager->flush();
    }
}

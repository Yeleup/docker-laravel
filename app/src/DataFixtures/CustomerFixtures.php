<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Market;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CustomerFixtures extends Fixture implements DependentFixtureInterface
{
    private $faker;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->faker = Factory::create();
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $min = $this->em->createQueryBuilder()
            ->select('MIN(m.id)')
            ->from(Market::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $max = $this->em->createQueryBuilder()
            ->select('MAX(m.id)')
            ->from(Market::class, 'm')
            ->getQuery()
            ->getSingleScalarResult();

        for ($i = 0; $i < 100; ++$i) {
            $market = $this->em->getRepository(Market::class)->find($this->faker->numberBetween($min, $max));
            $customer = new Customer();
            $customer->setName($this->faker->name);
            $customer->setContact($this->faker->phoneNumber);
            $customer->setMarket($market);
            $customer->setPlace($this->faker->streetAddress);
            $manager->persist($customer);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @psalm-return array<class-string<FixtureInterface>>
     */
    public function getDependencies()
    {
        return [
            MarketFixtures::class,
        ];
        // TODO: Implement getDependencies() method.
    }
}

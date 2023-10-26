<?php

namespace App\DataFixtures;

use App\Entity\Market;
use App\Entity\Payment;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $passwordHasher;
    private $faker;
    private $em;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create();
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        // Admin
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $user->setUsername('admin');
        $user->setPassword($this->passwordHasher->hashPassword($user, '147896'));
        $manager->persist($user);

        // User
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

        $market = $this->em->getRepository(Market::class)->find($this->faker->numberBetween($min, $max));

        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setUsername('magzhan');
        $user->setPassword($this->passwordHasher->hashPassword($user, '147896'));
        $user->addMarket($market);
        $manager->persist($user);

        // Control
        $min = $this->em->createQueryBuilder()
            ->select('MIN(p.id)')
            ->from(Payment::class, 'p')
            ->getQuery()
            ->getSingleScalarResult();

        $max = $this->em->createQueryBuilder()
            ->select('MAX(p.id)')
            ->from(Payment::class, 'p')
            ->getQuery()
            ->getSingleScalarResult();

        $payment = $this->em->getRepository(Payment::class)->find($this->faker->numberBetween($min, $max));

        $user = new User();
        $user->setRoles(['ROLE_CONTROL']);
        $user->setUsername('arman');
        $user->addPayment($payment);
        $user->setPassword($this->passwordHasher->hashPassword($user, '147896'));
        $manager->persist($user);

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
            PaymentFixtures::class,
        ];
        // TODO: Implement getDependencies() method.
    }
}

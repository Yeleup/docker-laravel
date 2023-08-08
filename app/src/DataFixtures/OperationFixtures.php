<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use App\Entity\Organization;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OperationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $operation = new Operation();
        $operation->setTitle('Долг');
        $manager->persist($operation);

        $operation = new Operation();
        $operation->setTitle('Приход');
        $operation->setRepayment(true);
        $manager->persist($operation);

        $operation = new Operation();
        $operation->setTitle('Возврат');
        $operation->setRepayment(true);
        $manager->persist($operation);

        $manager->flush();
    }
}

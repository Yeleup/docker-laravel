<?php

namespace App\DataFixtures;

use App\Entity\Payment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PaymentFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $payment = new Payment();
        $payment->setTitle('Наличными');
        $manager->persist($payment);

        $payment = new Payment();
        $payment->setTitle('Каспи Серік');
        $manager->persist($payment);

        $payment = new Payment();
        $payment->setTitle('Каспи Құланбике');
        $manager->persist($payment);

        $manager->flush();
    }
}

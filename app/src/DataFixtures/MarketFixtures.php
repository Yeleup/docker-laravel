<?php

namespace App\DataFixtures;

use App\Entity\Market;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MarketFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $market = new Market();
        $market->setTitle('Самал базар');
        $manager->persist($market);

        $market = new Market();
        $market->setTitle('Aйна базар');
        $manager->persist($market);

        $market = new Market();
        $market->setTitle('Бекжан базар');
        $manager->persist($market);

        $manager->flush();
    }
}

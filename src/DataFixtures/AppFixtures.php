<?php

namespace App\DataFixtures;

use App\Factory\CategoryFactory;
use App\Factory\CompanyFactory;
use App\Factory\OfferFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        CategoryFactory::createMany(20);
        CompanyFactory::createMany(20);
        UserFactory::createMany(5);
        OfferFactory::createMany(20);
    }
}

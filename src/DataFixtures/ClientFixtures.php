<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

final class ClientFixtures extends Fixture
{
    public const CLIENT_REFERENCE_PREFIX = 'client_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('pl_PL');
        
        for ($i = 1; $i <= 100; $i++) {
            $client = new Client();
            $client->setEmail($faker->unique()->safeEmail());

            if ($i % 3 === 0) {
                $client->setStripeCustomerId('cus_' . $faker->numberBetween(1, 99999));
            }

            $manager->persist($client);

            $this->addReference(self::CLIENT_REFERENCE_PREFIX . $i, $client);
        }

        $manager->flush();
    }
}
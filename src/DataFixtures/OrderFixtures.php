<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Order;
use App\Enum\PaymentStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

final class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $statuses = [PaymentStatus::NEW, PaymentStatus::PAID, PaymentStatus::REFUNDED];

        for ($i = 1; $i <= 100; $i++) {
            $order = new Order();

            $randomClientNumber = $faker->numberBetween(1, 100);
            /** @var Client $client */
            $client = $this->getReference(ClientFixtures::CLIENT_REFERENCE_PREFIX . $randomClientNumber, Client::class);
            $order->setClient($client);

            $order->setAmount($faker->randomFloat(2, 10, 1000));

            $status = $faker->randomElement($statuses);
            $order->setStatus($status);
            $order->setPaymentToken(Uuid::v4()->toRfc4122());

            if ($status !== PaymentStatus::NEW) {
                $order->setStripeSessionId('cs_test_' . $faker->numberBetween(1, 99999));
            }

            $manager->persist($order);
        }

        $manager->flush();
    }

    /**
     * Ta metoda informuje Doctrine, że najpierw musi wykonać się ClientFixtures,
     * a dopiero potem OrderFixtures.
     */
    public function getDependencies(): array
    {
        return [
            ClientFixtures::class,
        ];
    }
}
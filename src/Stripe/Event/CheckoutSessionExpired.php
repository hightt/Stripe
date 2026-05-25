<?php

declare(strict_types=1);

namespace App\Stripe\Event;

use App\Enum\PaymentStatus;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Event;

class CheckoutSessionExpired implements StripeWebhookInterface
{
    public const STRIPE_EVENT_NAME = 'checkout.session.expired';
    
    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function supports(string $eventIdentifier): bool
    {
        return self::STRIPE_EVENT_NAME === $eventIdentifier;
    }

    public function handle(Event $event): void
    {
        /** @var Session $session */
        $session = $event->data->object;
        $order = $this->orderRepository->findOneBy(['stripeSessionId' => $session->id]);

        if (!$order || $order->getStatus() === PaymentStatus::PAID) {
            return;
        }

        $order->setStatus(PaymentStatus::FAILED);
        $this->entityManager->flush();
    }
}
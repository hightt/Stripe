<?php

declare (strict_types = 1);

namespace App\Stripe\Event\Checkout;

use App\Entity\Order;
use App\Enum\PaymentStatus;
use App\Repository\OrderRepository;
use App\Stripe\Event\StripeWebhookInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Checkout\Session;
use Stripe\Event;

class CheckoutSessionCompleted implements StripeWebhookInterface
{
    public const STRIPE_EVENT_NAME = 'checkout.session.completed';

    public function __construct(
        private OrderRepository $orderRepository,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $stripeLogger,
    ) {}

    public function supports(string $eventIdentifier): bool
    {
        return self::STRIPE_EVENT_NAME === $eventIdentifier;
    }

    public function handle(Event $event): void
    {
        $stripeSessionObject = $event->data->object;
        $orderId             = $this->getOrderId($stripeSessionObject);
        if (is_null($orderId)) {
            return;
        }

        $order = $this->getOrderEntity($orderId, $stripeSessionObject);
        if (is_null($order) || $order->getStatus() === PaymentStatus::PAID) {
            return;
        }

        $order
            ->setStatus(PaymentStatus::PAID)
            ->setStripeSessionId($stripeSessionObject->id)
        ;

        $client = $order->getClient();
        if ($client && $stripeSessionObject->customer) {
            $client->setStripeCustomerId((string) $stripeSessionObject->customer);
        }

        $this->entityManager->flush();

        $this->stripeLogger->info('[Stripe Webhook] Order successfully marked as paid', [
            'order_id'          => $orderId,
            'stripe_session_id' => $stripeSessionObject->id,
        ]);
    }

    private function getOrderId(Session $stripeSessionObject): ?int
    {
        $orderId = $stripeSessionObject->metadata['order_id'] ?? null;
        if (is_null($orderId)) {
            $this->stripeLogger->error('[Stripe Webhook] Missing order_id in session metadata', [
                'stripe_session_id' => $stripeSessionObject->id,
            ]);

            return null;
        }

        return (int) $orderId;
    }

    private function getOrderEntity(int $orderId, Session $stripeSessionObject): ?Order
    {
        $order = $this->orderRepository->find((int) $orderId);
        if (is_null($order)) {
            $this->stripeLogger->error('[Stripe Webhook] Order not found', [
                'order_id'          => $orderId,
                'stripe_session_id' => $stripeSessionObject->id,
            ]);

            return null;
        }

        return $order;
    }
}

<?php

declare(strict_types=1);

namespace App\Stripe\Event\Checkout;

use App\Stripe\Event\StripeWebhookInterface;
use Stripe\Event;

class CheckoutSessionCompleted implements StripeWebhookInterface
{
    public const STRIPE_EVENT_NAME = 'charge.updated';
    
    public function supports(string $eventIdentifier): bool
    {
        return self::STRIPE_EVENT_NAME === $eventIdentifier;
    }

    public function handle(Event $event): void
    {
    }
}

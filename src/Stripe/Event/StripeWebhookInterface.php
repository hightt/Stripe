<?php

declare(strict_types=1);

namespace App\Stripe\Event;

use Stripe\Event;

interface StripeWebhookInterface
{
    public function supports(string $eventIdentifier): bool;

    public function handle(Event $event): void;
}

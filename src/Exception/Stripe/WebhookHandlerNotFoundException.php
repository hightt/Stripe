<?php

declare(strict_types=1);

namespace App\Exception\Stripe;

final class WebhookHandlerNotFoundException extends \RuntimeException
{
    public static function forType(string $eventType): self
    {
        return new self(sprintf('No registered webhook handler found for Stripe event type: "%s"', $eventType));
    }
}
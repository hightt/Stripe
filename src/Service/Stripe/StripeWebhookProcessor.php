<?php

declare(strict_types=1);

namespace App\Service\Stripe;

use App\Stripe\Event\StripeWebhookInterface;
use App\Exception\Stripe\WebhookHandlerNotFoundException;
use App\Stripe\StripeConfigurationService;
use Stripe\Event;
use Stripe\Webhook;

final readonly class StripeWebhookProcessor
{
    /**
     * @param iterable<StripeWebhookInterface> $handlers
     */
    public function __construct(
        private iterable $handlers ,
        private StripeConfigurationService $stripeConfiguration,
    ) {}

    public function parseAndProcess(string $payload, string $sigHeader): Event
    {
        $event = Webhook::constructEvent($payload, $sigHeader, $this->stripeConfiguration->webhookSecret);
        foreach ($this->handlers as $handler) {
            if ($handler->supports($event->type)) {
                $handler->handle($event);
                
                return $event;
            }
        }

        throw new WebhookHandlerNotFoundException();
    }
}
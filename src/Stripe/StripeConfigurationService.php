<?php

declare(strict_types=1);

namespace App\Stripe;

final readonly class StripeConfigurationService
{
    public function __construct(
        public string $webhookSecret,
        public string $secretKey,
    ) {}
}
<?php

declare(strict_types=1);

namespace App\Stripe;

interface PaymentGatewayInterface
{
    public function createCheckoutSessionUrl(int $orderId, float $amount, string $successUrl, string $cancelUrl): string;
}
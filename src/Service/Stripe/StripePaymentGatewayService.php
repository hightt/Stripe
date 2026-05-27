<?php

declare(strict_types=1);

namespace App\Service\Stripe;

use App\Stripe\PaymentGatewayInterface;
use Stripe\StripeClient;

class StripePaymentGatewayService implements PaymentGatewayInterface
{
    public function __construct(
        private StripeClient $stripe,
    ) {}

    public function createCheckoutSessionUrl(int $orderId, float $amount, string $successUrl, string $cancelUrl): string
    {
        $amountInCents = (int) round($amount * 100);

        $session = $this->stripe->checkout->sessions->create([
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'mode'        => 'payment',
            'line_items'  => [[
                'price_data' => [
                    'currency'     => 'pln',
                    'unit_amount'  => $amountInCents,
                    'product_data' => ['name' => sprintf('Zamówienie #%d', $orderId)],
                ],
                'quantity'   => 1,
            ]],
            'metadata' => ['order_id' => $orderId],
        ]);

        return $session->url;
    }
}
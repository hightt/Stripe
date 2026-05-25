<?php

declare (strict_types = 1);

namespace App\Service\Stripe;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Stripe\StripeConfigurationService;
use Stripe\StripeClient;

class StripeCheckoutService
{
    public function __construct(
        private StripeConfigurationService $stripeConfiguration,
        private StripePaymentService $stripePaymentService,
        private OrderRepository $orderRepository,
    ) {}

    public function createCheckoutSession(Order $order): string
    {
        $successUrl    = $this->stripePaymentService->createPaymentStatusUrl('success', $order->getId());
        $cancelUrl     = $this->stripePaymentService->createPaymentStatusUrl('failure', $order->getId());
        $amountInCents = (int) round($order->getAmount() * 100);
        $stripe        = new StripeClient($this->stripeConfiguration->secretKey);
        $session       = $stripe->checkout->sessions->create([
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'line_items'  => [
                [
                    'price_data' => [
                        'currency'     => 'pln',
                        'unit_amount'  => $amountInCents,
                        'product_data' => [
                            'name' => sprintf('Zamówienie #%d', $order->getId()),
                        ],
                    ],
                    'quantity'   => 1,
                ],
            ],
            'mode'        => 'payment',
            'metadata'    => [
                'order_id' => $order->getId(),
            ],
        ]);

        return $session->url;
    }
}

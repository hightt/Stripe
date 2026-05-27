<?php

declare (strict_types = 1);

namespace App\Service\Stripe;

use App\Entity\Order;
use App\Stripe\PaymentGatewayInterface;

class StripeCheckoutService
{
    public function __construct(
        private StripePaymentService $stripePaymentService,
        private PaymentGatewayInterface $paymentGatewayInterface,
    ) {}

    public function createCheckoutSession(Order $order): string
    {
        $successUrl    = $this->stripePaymentService->createPaymentStatusUrl('success', $order->getId());
        $cancelUrl     = $this->stripePaymentService->createPaymentStatusUrl('failure', $order->getId());
        $amountInCents = (int) round($order->getAmount() * 100);
        $sessionUrl    = $this->paymentGatewayInterface->createCheckoutSessionUrl($order->getId(), $amountInCents, $successUrl, $cancelUrl);

        return $sessionUrl;
    }
}

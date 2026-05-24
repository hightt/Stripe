<?php

declare(strict_types=1);

namespace App\Service\Stripe;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripePaymentService
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    )
    {}

    public function createPaymentStatusUrl(string $status, int $orderId): string
    {
        return $this->urlGenerator->generate(sprintf('app_payment_%s', $status), ['order_id' => $orderId], UrlGeneratorInterface::ABSOLUTE_URL);
    }
}

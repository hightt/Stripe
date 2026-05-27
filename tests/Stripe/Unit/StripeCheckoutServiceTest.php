<?php

declare (strict_types = 1);

namespace App\Tests\Unit;

use App\Entity\Order;
use App\Service\Stripe\StripeCheckoutService;
use App\Service\Stripe\StripePaymentService;
use App\Stripe\PaymentGatewayInterface;
use PHPUnit\Framework\TestCase;

class StripeCheckoutServiceTest extends TestCase
{
    /** @var StripePaymentService&\PHPUnit\Framework\MockObject\MockObject */
    private StripePaymentService $stripePaymentService;

    /** @var PaymentGatewayInterface&\PHPUnit\Framework\MockObject\MockObject */
    private PaymentGatewayInterface $paymentGatewayInterface;

    private StripeCheckoutService $stripeCheckoutService;

    protected function setUp(): void
    {
        $this->stripePaymentService = $this->createMock(StripePaymentService::class);
        $this->paymentGatewayInterface = $this->createMock(PaymentGatewayInterface::class);
        $this->stripeCheckoutService = new StripeCheckoutService(
            $this->stripePaymentService,
            $this->paymentGatewayInterface,
        );
    }

    public function testCreateCheckoutSession(): void
    {
        $order = $this->createMock(Order::class);
        $order->method('getId')->willReturn(1);
        $order->method('getAmount')->willReturn(100.50);

        $urlMap = [
            ['success', 1, 'https://success.com'],
            ['failure', 1, 'https://failure.com'],
        ];
        $this->stripePaymentService
            ->method('createPaymentStatusUrl')
            ->willReturnMap($urlMap);

        $expectedUrl = 'https://checkout.stripe.com/pay/cs_test_123';
        $this->paymentGatewayInterface
            ->method('createCheckoutSessionUrl')
            ->with(
                1,             
                10050,         
                'https://success.com',
                'https://failure.com'
            )
            ->willReturn($expectedUrl);

        $resultUrl = $this->stripeCheckoutService->createCheckoutSession($order);

        $this->assertEquals($expectedUrl, $resultUrl);
    }
}
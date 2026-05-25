<?php

declare(strict_types=1);

namespace App\Controller\Api\V1\Stripe;

use App\Enum\PaymentStatus;
use App\Repository\OrderRepository;
use App\Service\Stripe\StripeCheckoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/{paymentToken}/pay', name: 'app_order_pay', methods: ['GET'])]
    public function pay(
        string $paymentToken,
        OrderRepository $orderRepository,
        StripeCheckoutService $stripeCheckoutService,
    ): Response {
        $order = $orderRepository->findOneByPaymentToken($paymentToken);

        if (!$order || $order->getStatus() !== PaymentStatus::NEW) {
            throw $this->createNotFoundException('Zamówienie nie istnieje lub zostało już opłacone.');
        }

        $stripeUrl = $stripeCheckoutService->createCheckoutSession($order);

        return $this->redirect($stripeUrl);
    }
}
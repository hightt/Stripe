<?php

declare(strict_types=1);

namespace App\Controller\Api\V1\Stripe;

use App\Repository\OrderRepository;
use App\Service\Stripe\StripeCheckoutService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/order/{orderId}/pay', name: 'app_order_pay', methods: ['GET'])]
    public function pay(
        int $orderId,
        OrderRepository $orderRepository,
        StripeCheckoutService $stripeCheckoutService,
    ): Response {
        $order = $orderRepository->find($orderId);

        if (!$order || $order->getStatus() !== 'new') {
            throw $this->createNotFoundException('Zamówienie nie istnieje lub zostało już opłacone.');
        }

        $stripeUrl = $stripeCheckoutService->createCheckoutSession($orderId);

        return $this->redirect($stripeUrl);
    }
}
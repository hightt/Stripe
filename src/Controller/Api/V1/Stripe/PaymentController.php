<?php

declare (strict_types=1);

namespace App\Controller\Api\V1\Stripe;

use App\Repository\OrderRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    public function __construct(
        private OrderRepository $orderRepository,
    ) {}
    
    #[Route('/payment/success/{order_id}', name: 'app_payment_success', methods: ['GET'])]
    public function success(int $order_id): Response
    {
        $order = $this->orderRepository->find($order_id);

        if (!$order) {
            throw new Exception('Nie znaleziono takiego zamówienia.');
        }

        return $this->render('payment/success.html.twig', [
            'order'  => $order,
            'client' => $order->getClient(),
        ]);
    }

    #[Route('/payment/failure/{order_id}', name: 'app_payment_failure', methods: ['GET'])]
    public function failure(int $order_id): Response
    {
        $order = $this->orderRepository->find($order_id);
        if (! $order) {
            throw $this->createNotFoundException('Nie znaleziono takiego zamówienia.');
        }

        return $this->render('payment/failure.html.twig', [
            'order' => $order,
        ]);
    }
}

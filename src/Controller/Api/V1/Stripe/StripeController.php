<?php

declare(strict_types=1);

namespace App\Controller\Api\V1\Stripe;

use App\Service\Stripe\StripeWebhookProcessor;
use App\Exception\Stripe\WebhookHandlerNotFoundException;
use Psr\Log\LoggerInterface;
use Stripe\Exception\SignatureVerificationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UnexpectedValueException;

final class StripeController extends AbstractController
{
    #[Route('/api/v1/stripe', name: 'app_api_v1_stripe_stripe', methods: ['POST'])]
    public function __invoke(
        Request $request,
        StripeWebhookProcessor $stripeWebhookProcessor,
        LoggerInterface $stripeLogger,
    ): JsonResponse {
        $stripeLogger->info('Stripe webhook request received');
        $sigHeader = $request->headers->get('Stripe-Signature');
        if (!$sigHeader) {
            $stripeLogger->warning('Missing Stripe-Signature header in request');
            
            return $this->json(['error' => 'Missing signature header'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $event = $stripeWebhookProcessor->parseAndProcess((string) $request->getContent(), $sigHeader);
            $stripeLogger->info('Successfully processed webhook', ['type' => $event->type]);
            
            return $this->json(['status' => 'success'], Response::HTTP_OK);
        } catch (UnexpectedValueException $e) {
            $stripeLogger->error('Invalid Stripe payload received', ['exception' => $e]);
            
            return $this->json(['error' => 'Invalid payload'], Response::HTTP_BAD_REQUEST);
        } catch (SignatureVerificationException $e) {
            $stripeLogger->error('Stripe signature verification failed', ['exception' => $e]);
            
            return $this->json(['error' => 'Invalid signature'], Response::HTTP_BAD_REQUEST);
        } catch (WebhookHandlerNotFoundException $e) {
            $stripeLogger->info($e->getMessage());

            return $this->json(['message' => 'Event received but not handled'], Response::HTTP_OK);
        }
    }
}
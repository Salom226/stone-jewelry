<?php

namespace App\Controller;

use App\Service\StripePayment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StripeController extends AbstractController
{
    private StripePayment $stripePayment;

    public function __construct(StripePayment $stripePayment)
    {
        $this->stripePayment = $stripePayment;
    }

    // #[Route('/api/stripe/create-session', name: 'api_stripe_create_session', methods: ['POST'])]
    // public function createSession(Request $request): JsonResponse
    // // {
    //     $data = json_decode($request->getContent(), true);

    //     $this->stripePayment->startPayment($data['cart'], $data['shippingCost']);

    //     return new JsonResponse([
    //         'id' => $this->stripePayment->getStripeRedirectUrl(),
    //     ]);
    // }
    // #[Route('/pay/success', name: 'app_stripe_success')]
    // public function success(): Response
    // {
    //     return $this->render('stripe/index.html.twig', [
    //         'controller_name' => 'StripeController',
    //     ]);
    // }

    // #[Route('/pay/cancel', name: 'app_stripe_cancel')]
    // public function cancel(): Response
    // {
    //     return $this->render('stripe/index.html.twig', [
    //         'controller_name' => 'StripeController',
    //     ]);
    // }
}

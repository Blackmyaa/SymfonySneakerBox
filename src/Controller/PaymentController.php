<?php

// src/Controller/PaymentController.php
namespace App\Controller;

use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    
    #[Route("/create-payment-intent", name:"create_payment_intent", methods:"POST")]
    public function createPaymentIntent(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $amount = $data['amount'];
        $currency = $data['currency'];

        $paymentIntent = $this->stripeService->createPaymentIntent($amount, $currency);

        return new Response(json_encode(['clientSecret' => $paymentIntent->client_secret]), 200, ['Content-Type' => 'application/json']);
    }

    #[Route("/checkout", name:"checkout")]
    public function checkout(): Response
    {
        return $this->render('payment/checkout.html.twig', [
            'stripePublicKey' => $_ENV['STRIPE_PUBLIC_KEY'],
        ]);
    }
}
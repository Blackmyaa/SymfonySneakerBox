<?php 

// src/Service/StripeService.php
namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeService
{
    private $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
        Stripe::setApiKey($this->secretKey);
    }

    public function createPaymentIntent($amount, $currency)
    {
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }
}


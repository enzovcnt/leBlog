<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class StripeController extends AbstractController
{
    #[Route('/create-checkout-session', name: 'stripe_checkout', methods: ['POST'])]
    public function createSession(): JsonResponse
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => ['name' => 'Don'],
                    'unit_amount' => 100, // 1 USD = 100 cents
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('don_success', [], 0),
            'cancel_url' => $this->generateUrl('don_cancel', [], 0),
        ]);

        return new JsonResponse(['id' => $session->id]);
    }

    #[Route('/don-success', name: 'don_success')]
    public function success(): Response
    {
        return $this->render('stripe/success.html.twig');
    }

    #[Route('/don-cancel', name: 'don_cancel')]
    public function cancel(): Response
    {
        return $this->render('stripe/fail.html.twig');
    }
}

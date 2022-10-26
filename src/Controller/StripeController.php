<?php

namespace App\Controller;

use App\Class\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session', name: 'app_stripe')]
    public function index(Cart $cart): Response
    {

        header('Content-Type: application/json');

        $YOUR_DOMAIN = 'http://127.0.0.1:8000' ;
        $product_for_stripe = [];
        foreach ($cart->getFull() as $product) {
            $product_for_stripe[] = [
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                //'price' => $product['product']->getPrice(),
                'price_data'=>[
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
                    ]
                ],
                'quantity' => $product['quantity'],
            ];
        }

        Stripe::setApiKey("sk_test_51LwVfABPq6Kh4m0lysru3l2yqqsru9oinHiO0aAiUptOPVylGJtM9Z1IhzOOFQ05gVoVhsVOUuL2P94kR7qhSO0200mEu9HyJ6");
        $checkoutSession = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$product_for_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN.'/success',
            'cancel_url' => $YOUR_DOMAIN.'/cancel',
        ]);

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkoutSession->url);

        return $this->redirect($checkoutSession->url, 303);

    }
}

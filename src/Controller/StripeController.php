<?php

namespace App\Controller;

use App\Class\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session/{reference}', name: 'app_stripe')]
    public function index(EntityManagerInterface $entityManager,Cart $cart, $reference): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000' ;
        $product_for_stripe = [];


        $order = $entityManager->getRepository(Order::class)->findOneBy(['reference' => $reference]);

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $entityManager->getRepository(Product::class)->findOneBy(['name' => $product->getProduct()]);
            $product_for_stripe[] = [
                # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
                //'price' => $product['product']->getPrice(),
                'price_data'=>[
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ]
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $product_for_stripe[] = [
            # Provide the exact Price ID (e.g. pr_1234) of the product you want to sell
            //'price' => $product['product']->getPrice(),
            'price_data'=>[
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ]
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey("sk_test_51LwVfABPq6Kh4m0lysru3l2yqqsru9oinHiO0aAiUptOPVylGJtM9Z1IhzOOFQ05gVoVhsVOUuL2P94kR7qhSO0200mEu9HyJ6");
        $checkoutSession = Session::create([
            'customer_email' => $this->getUser()->getUserIdentifier(),
            'payment_method_types' => ['card'],
            'line_items' => [$product_for_stripe],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN.'/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN.'/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkoutSession->id);
        $entityManager->flush();

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkoutSession->url);

        return $this->redirect($checkoutSession->url, 303);

    }
}

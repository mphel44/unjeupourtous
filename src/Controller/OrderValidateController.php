<?php

namespace App\Controller;

use App\Class\Cart;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    private $entityManager ;
    private $orderRepository;

    public function __construct(EntityManagerInterface $entityManager, OrderRepository $orderRepository) {
        $this->entityManager = $entityManager ;
        $this->orderRepository = $orderRepository ;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->orderRepository->findOneBy(['stripeSessionId' => $stripeSessionId]);
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home_index');
        }

        if (!$order->isIsPaid()) {
            //Vider cart
            $cart->remove();

            //change is paid
            $order->setIsPaid(1);
            $this->entityManager->flush();

            //envoyer mail au client
        }


        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}
<?php

namespace App\Controller;

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
    public function index($stripeSessionId): Response
    {
        $order = $this->orderRepository->findOneBy(['stripeSessionId' => $stripeSessionId]);
        dd($order) ;
        return $this->render('order_validate/index.html.twig');
    }
}

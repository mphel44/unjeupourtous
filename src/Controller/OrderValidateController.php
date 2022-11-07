<?php

namespace App\Controller;

use App\Class\Cart;
use App\Class\Mail;
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

        if ($order->getState() == 0) {
            //Vider cart
            $cart->remove();

            //change is paid
            $order->setState(1);
            $this->entityManager->flush();

            //envoyer mail au client
            $mail = new Mail();
            $content = 'Bonjour '.$order->getUser()->getFirstname().'. Votre commande sur Un Jeu Pour Tous est réussie ! '.
                'Vous pouvez pouvez désormais suivre votre commande à cette adresse.' ;
            $mail->sendEmail($order->getUser()->getEmail(), $order->getUser()->getFirstname(),
                            'Un jeu pour tous - commandé réussie',
                                'Commande réussie', $content);

        }


        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}

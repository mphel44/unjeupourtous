<?php

namespace App\Class;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    private $requestStack;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }

    public function add($id){
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart  [$id] = 1 ;
        }

        $session->set('cart', $cart) ;
    }

    public function get(){
        $session = $this->requestStack->getSession();
        return $session->get('cart');
    }

    public function getFull(){
        $cartComplete = [];
        if ($this ->get()) {
            foreach ($this ->get() as $id => $quantity) {
                $productObjet = $this->entityManager->getRepository(Product::class)->findOneBy(['id' =>$id]) ;
                //si id n'existe pas, suppression de l'objet du panier
                if (!$productObjet){
                    $this->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $productObjet,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }



    public function remove(){
        $session = $this->requestStack->getSession();
        return $session->remove('cart');
    }

    public function delete($id){
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
        unset($cart[$id]) ;
        return $session->set('cart', $cart);
    }

    public function decrease($id){
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart', []);
         if ($cart[$id] > 1 ) {
             $cart[$id]--;
         } else {
             unset($cart[$id]) ;
         }
        $session->set('cart', $cart) ;
    }


}

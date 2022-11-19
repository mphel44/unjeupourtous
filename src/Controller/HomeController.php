<?php

namespace App\Controller;

use App\Class\Mail;
use App\Repository\HeaderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{



    #[Route('/', name: 'home_index')]
    public function index(ProductRepository $productRepository, HeaderRepository $headerRepository): Response
    {
        $products = $productRepository->findBy(['isBest' => true]);
        $headers = $headerRepository->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'headers' => $headers
        ] );
    }
}

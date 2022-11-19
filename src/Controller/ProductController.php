<?php

namespace App\Controller;

use App\Class\Search;
use App\Entity\Product;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/nos-produits', name: 'app_product')]
    public function index(Request $request): Response
    {

        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);

        } else {
            $products = $this->entityManager->getRepository(Product::class)->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product_detail')]
    public function show(ProductRepository $productRepository, $slug): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneBy(array('slug' => $slug));
        $products = $productRepository->findBy(['isBest' => true]);

        if (!$product){
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'products' => $products
        ]);
    }


}

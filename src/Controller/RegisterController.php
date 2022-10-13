<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;

class RegisterController extends AbstractController
{
    private $entityManager ;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager ;
    }

    #[Route('/inscription', name: 'inscription_index')]
    public function index(Request $request, UserPasswordHasherInterface $hasher)
    {
        $user = new User() ;
        $form = $this->createForm(RegisterType::class, $user) ;
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()){
            $user = $form->getData() ;
            $password = $hasher->hashPassword($user, $user->getPassword()) ;
            $user->setPassword($password) ;
            $this->entityManager->persist($user) ;
            $this->entityManager->flush();
            return $this->render('home/index.html.twig');
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

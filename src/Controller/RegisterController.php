<?php

namespace App\Controller;

use App\Class\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
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
    public function index(Request $request, UserPasswordHasherInterface $hasher, UserRepository $userRepository)
    {
        $notification = null ;

        $user = new User() ;
        $form = $this->createForm(RegisterType::class, $user) ;
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData() ;

            $searchEmail = $userRepository->findOneBy(['email' =>$user->getEmail()]);
            if (!$searchEmail) {
                $password = $hasher->hashPassword($user, $user->getPassword()) ;
                $user->setPassword($password) ;
                $this->entityManager->persist($user) ;
                $this->entityManager->flush();

                $mail = new Mail();
                $content = 'Bonjour '.$user->getFirstname().'. Votre inscription sur le site Un Jeu Pour Tous est réussie ! '.
                    'Vous pouvez désormais utiliser le site afin d\'acheter et/ou de vendre des jeux ! Bonne navigation' ;
                $mail->sendEmail($user->getEmail(), $user->getFirstname(), 'Bienvenu sur Un jeu pour tous', 'Inscription réussie', $content);

                $notification = 'Votre inscription s\'est correctement déroulée. 
                                Vous pouvez dès à présent vous connecter' ;
            } else {
                $notification = 'L\'email existe déjà' ;
            }

            //return $this->render('home/index.html.twig', ['notification' => $notification]);
        }
        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}

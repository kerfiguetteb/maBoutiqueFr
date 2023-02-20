<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{

    #[Route('/inscription', name: 'app_register')]    
    /**
     * index
     *
     * @param  mixed $request
     * @param  mixed $encoder
     * @return Response
     */
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form  = $this->createForm(RegisterType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
           $user->setPassword($userPasswordHasher->hashPassword($user, $form->get('password')->getData())); 
            $userRepository->save($user, true);
            
        }
        
        return $this->render('register/index.html.twig',[
            'form' => $form->createView()
        ]);
    }
}

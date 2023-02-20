<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountPasswordController extends AbstractController
{
    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $hacher): Response
    {
        $user = $this->getUser();
        $form  = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        $notification = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
            if ($hacher->isPasswordValid($user, $old_pwd)) {
                $new_pwd = $form->get('new_password')->getData();
                $password = $hacher->hashPassword($user, $new_pwd);
                $user->setPassword($password);
                $userRepository->save($user, true);
                $notification = true;
            }
            else{
                $notification = false;
            }
        }
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification

        ]);
    }
}

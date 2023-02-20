<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte')]
class OrderCancelController extends AbstractController
{
    #[Route('/commande/erreur/{stripeSessionId}', name: 'app_order_erreur')]
    public function index(OrderRepository $orderRepository, $stripeSessionId): Response
    {
        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser() ){
          return $this->redirectToRoute('app_home');  
        }



        return $this->render('order_cancel/index.html.twig',[
            'order' => $order
        ]);
    }
}

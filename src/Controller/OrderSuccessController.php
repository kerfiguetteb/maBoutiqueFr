<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/compte')]
class OrderSuccessController extends AbstractController
{
    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_success')]
    public function index(Cart $cart, OrderRepository $orderRepository, $stripeSessionId): Response
    {
        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser() ){
          return $this->redirectToRoute('app_home');  
        }

        if($order->getState() == 0){
            $cart->remove();
            $order->setState(1);
            $orderRepository->save($order, true);
        }

        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}

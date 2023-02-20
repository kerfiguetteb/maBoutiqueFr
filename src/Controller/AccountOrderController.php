<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/compte')]
class AccountOrderController extends AbstractController
{
    #[Route('/mes-commandes', name: 'app_account_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findSuccessOrder($this->getUser()); 
        // dd($orders);
        return $this->render('account/order/index.html.twig',[
            'orders'=>$orders
        ]);
    }
        #[Route('/mes-commandes/{reference}', name: 'app_account_order_show')]
    public function show(OrderRepository $orderRepository, $reference): Response
    {
        $order = $orderRepository->findOneByReference($reference); 
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_order');
        }

        return $this->render('account/order/show.html.twig',[
            'order'=>$order
        ]);
    }
    }

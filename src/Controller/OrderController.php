<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte')]
class OrderController extends AbstractController
{
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('app_add_address');
        }

        $form = $this->createForm(OrderType::class, null,[
            'user'=>$this->getUser()
        ]);
        return $this->render('order/index.html.twig',[
            'form'=>$form->createView(),
            'cart'=>$cart->getFull()
        ]);
    }
    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods:['POST'])]
    public function add(Cart $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null,[
            'user'=>$this->getUser()
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $date = new DateTimeImmutable();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '</br>'.$delivery->getPhone();

            if($delivery->getCompany()){
                $delivery_content .= '</br>'.$delivery->getCompany();
            }
            $delivery_content .= '</br>'.$delivery->getAddress();
            $delivery_content .= '</br>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '</br>'.$delivery->getCountry();
            
            $order = new Order();
            $reference = $date->format('dmY').'_'.uniqid();
            $order->setReference($reference);
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);
            $this->entityManager->persist($order);


            foreach($cart->getFull() as $product){
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $total = $product['product']->getPrice()*$product['quantity'];
                $orderDetails->setTotal($total);
                $this->entityManager->persist($orderDetails);

            }

            $this->entityManager->flush();

            $stripeSecretKey = 'sk_test_51MZbZnGxhNGcSEtOMZqORyqDs2cnRPEBZKWS1wtOmk1yZRDPrpBZDxhouUNl9FekAv14Vu8JQuRnnIcNaCdfqZVU00Fjf1MFLW';
            Stripe::setApiKey($stripeSecretKey);


            // header("HTTP/1.1 303 See Other");
            // header("Location: " . $checkout_session->url);
            return $this->render('order/add.html.twig',[
                'cart'=>$cart->getFull(),
                'carrier'=>$carriers,
                'delivery'=>$delivery_content,
                'reference'=>$order->getReference()
                
            ]);
    
        }
        return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);

    }
}

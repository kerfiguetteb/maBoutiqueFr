<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte/commande')]
class StripeController extends AbstractController
{
    #[Route('/create-session/{reference}', name: 'app_stripe')]
    public function index(OrderRepository $orderRepository,ProductRepository $productRepository, Cart $cart, $reference){
        
        $YOUR_DOMAIN = 'https://127.0.0.1:8000';
        $product_for_stripe = [];

        $order = $orderRepository->findOneByReference($reference);
        $order->getOrderDetails()->getValues();

        if(!$order){
            return $this->redirect('app_order');

        }
        foreach($order->getOrderDetails()->getValues()as $product){
            $product_object = $productRepository->findOneByName($product->getProduct());
            $product_for_stripe[] = [
                'price_data'=>[
                    'currency'=> 'eur',
                    'unit_amount'=>$product->getPrice(),
                    'product_data'=>[
                        'name'=>$product->getProduct(),
                        'images'=>[$YOUR_DOMAIN."/uploads/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
    
            ];

        }   
        $product_for_stripe[] = [
            'price_data'=>[
                'currency'=> 'eur',
                'unit_amount'=>$order->getCarrierPrice(),
                'product_data'=>[
                    'name'=>$order->getCarrierName(),
                    'images'=>[$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        $stripeSecretKey = 'sk_test_51MZbZnGxhNGcSEtOMZqORyqDs2cnRPEBZKWS1wtOmk1yZRDPrpBZDxhouUNl9FekAv14Vu8JQuRnnIcNaCdfqZVU00Fjf1MFLW';
        Stripe::setApiKey($stripeSecretKey);

        $checkout_session = Session::create([
            'customer_email'=>$this->getUser()->getEmail(),
          'payment_method_types'=> ['card'],
          'line_items' => [$product_for_stripe
          ],
          'mode' => 'payment',
          'success_url' => $YOUR_DOMAIN . '/compte/commande/merci/{CHECKOUT_SESSION_ID}',
          'cancel_url' => $YOUR_DOMAIN . '/compte/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $orderRepository->save($order,true);
        return $this->redirect($checkout_session->url);
        }
}

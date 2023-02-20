<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'app_cart')]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig',[
            'cart' => $cart->getFull()
        ]);
    }
   
    #[Route('/cart/add/{id}', name: 'app_add_cart')]
    public function add(Cart $cart, $id): Response
    {
        $cart->add($id);
        return $this->redirecttoRoute('app_cart');
    }
    
    #[Route('/cart/remove', name: 'app_remove_cart')]
    public function remove(Cart $cart): Response
    {
        $cart->remove($cart);
        return $this->redirecttoRoute('app_product');
    }

    #[Route('/cart/delete/{id}', name: 'app_delete_cart')]
    public function delete(Cart $cart,$id): Response
    {
        $cart->delete($id);
        return $this->redirecttoRoute('app_cart');
    }
    
    #[Route('/cart/decrease/{id}', name: 'app_decrease_cart')]
    public function decrease(Cart $cart,$id): Response
    {
        $cart->decrease($id);
        return $this->redirecttoRoute('app_cart');
    }
}

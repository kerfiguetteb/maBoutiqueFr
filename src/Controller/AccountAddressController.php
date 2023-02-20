<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/compte')]
class AccountAddressController extends AbstractController
{
    private $addressRepository;

    public function __construct(AddressRepository $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    #[Route('/address', name: 'app_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }
    
    #[Route('/ajouter-une-adresse', name: 'app_add_address')]
    public function add(Cart $cart, Request $request) : Response
    {
        $adresse = new Address();
        $form  = $this->createForm(AddressType::class, $adresse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $adresse->setUser($this->getUser());
            $this->addressRepository->save($adresse, true);
            if($cart->get()){
                return $this->redirectToRoute('app_order');
            }else{

                return $this->redirectToRoute('app_address');
            }
        }

        return $this->render('account/address_form.html.twig',[
            'form'=> $form->createView()
        ]);
    }
    
    #[Route('/modifier-une-adresse/{id}', name: 'app_address_edit')]
    public function edit(Request $request,$id) : Response
    {
        $adresse = $this->addressRepository->find($id);
        if(!$adresse || $adresse->getUser() != $this->getUser()){
            return $this->redirectToRoute('app_address');

        }
        $form  = $this->createForm(AddressType::class, $adresse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->addressRepository->save($adresse, true);
            return $this->redirectToRoute('app_address');
        }

        return $this->render('account/address_form.html.twig',[
            'form'=> $form->createView()
        ]);
    }
    #[Route('/supprimer-une-adresse/{id}', name: 'app_address_delete')]
    public function delete($id) : Response
    {
        $adresse = $this->addressRepository->find($id);
       
        if($adresse && $adresse->getUser() == $this->getUser()){
            $this->addressRepository->remove($adresse, true);
        }
            return $this->redirectToRoute('app_address');

    }
}

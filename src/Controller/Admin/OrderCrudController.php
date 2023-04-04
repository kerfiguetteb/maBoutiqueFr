<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }
    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fa fa-box-open')
        ->linkToCrudAction('updatePreparation');
       
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fa fa-truck')
        ->linkToCrudAction('updateDelivery');
        
        return $actions
            ->add('index', 'detail')
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery);
    }

    public function updatePreparation(AdminContext $context,
     OrderRepository $orderRepository
     ){
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $orderRepository->save($order, true);

        $this->addFlash('notice', "<span style='color:green;'><strong>La commande ".$order->getReference()." <u> est en cours de préparation </strong></span>");

        $url = $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::INDEX)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

         return $this->redirect($url);
    }

    
    public function updateDelivery(AdminContext $context,
     OrderRepository $orderRepository
     ){
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $orderRepository->save($order, true);

        $this->addFlash('notice', "<span style='color:orange;'><strong>La commande ".$order->getReference()." <u> est en cours de livraiosn </strong></span>");

        $url = $this->container->get(AdminUrlGenerator::class)
                ->setAction(Action::INDEX)
                ->setEntityId($context->getEntity()->getPrimaryKeyValue())
                ->generateUrl();

         return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id'=>'DESC']);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('created_at', 'Passé le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextField::new('delivery','Adresse de livraison')->onlyOndetail()->renderAsHtml(),
            MoneyField::new('total', 'Total produits')->setCurrency('EUR'),
            TextField::new('carrierName', 'Livreur'),
            MoneyField::new('carrierPrice','frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée'=> 0,
                'Payée'=> 1,
                'Préparation en cours'=> 2,
                'Livraison en cours'=> 3,
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }
}


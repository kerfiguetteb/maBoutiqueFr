<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        
        return $this->redirect($adminUrlGenerator->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Laboutiquefr');
    }

    public function configureMenuItems(): iterable
    {
        return[

            MenuItem::linkToDashboard('Home', 'fa fa-home'),
            //fontawesome
            MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class),
            MenuItem::linkToCrud('Categorie', 'fa fa-list', Category::class),
            MenuItem::linkToCrud('Produit', 'fa fa-tag', Product::class),
            MenuItem::linkToCrud('Livreur', 'fa fa-truck', Carrier::class),
            MenuItem::linkToCrud('Commande', 'fa fa-shopping-cart', Order::class)
        ];
    }
}

<?php

namespace App\Controller\User;

use App\Entity\Market;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserDashboardController extends AbstractDashboardController
{
    private $translator;
    private $adminUrlGenerator;

    public function __construct(TranslatorInterface $translator, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->translator = $translator;
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        if ($this->isGranted('ROLE_USER')) {
            $market = $this->getDoctrine()->getRepository(Market::class)->findByUserMarket($this->getUser())[0];

            return $this->redirect($this->adminUrlGenerator->setRoute('user_customer', ['id' => $market->getId()])->generateUrl());
        }

        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        $title = $this->translator->trans('header.name');

        return Dashboard::new()->setTitle($title);
    }

    public function configureMenuItems(): iterable
    {
        $markets = $this->getDoctrine()->getRepository(Market::class)->findByUserMarket($this->getUser());

        $menuMarket = [];
        foreach ($markets as $market) {
            $menuMarket[] = MenuItem::linkToRoute($market->getTitle(), 'fas fa-users', 'user_customer', ['id' => $market->getId()]);
        }

        yield MenuItem::subMenu('dashboard.market', 'fas fa-sitemap')->setSubItems($menuMarket);
    }
}

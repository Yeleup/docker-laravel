<?php

namespace App\Controller\User;

use App\Entity\Customer;
use App\Entity\Market;
use App\Repository\MarketRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

class CustomerController extends AbstractController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    /**
     * @Route("user/customer/{id}", name="user_customer")
     */
    public function index(Request $request, Market $market, PaginatorInterface $paginator): Response
    {
        $customerRepository = $this->getDoctrine()->getRepository(Customer::class);

        // GET
        $params = [];
        if ($market) {
            $params['market'] = $market->getId();
        }

        $params['search'] = $request->query->get('search');

        if (!$request->query->get('order')) {
            $request->query->set('order', 'ASC');
        }

        $params['order'] = $request->query->get('order');

        if (!$request->query->get('sorting')) {
            $request->query->set('sorting', 'c.name');
        }

        $params['sorting'] = $request->query->get('sorting');

        if (!$request->query->get('page')) {
            $request->query->set('page', 1);
        }

        $params['page'] = $request->query->get('page');

        // Список клиентов
        $filter_data = [
            'market' => $market,
            'search' => $request->query->get('search'),
            'sort' => $request->query->get('sorting'),
            'order' => $request->query->get('order'),
        ];

        $pagination = $paginator->paginate(
            $customerRepository->findByFilter($filter_data),
            $request->query->getInt('page', $request->query->get('page')),
            5
        );

        $data['customer'] = [];

        foreach ($pagination->getItems() as $customer) {
            $href = $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])
                ->setAll($params)
                ->generateUrl();

            $data['customer'][] = [
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'place' => $customer->getPlace(),
                'contact' => $customer->getContact(),
                'total' => $customer->getTotal(),
                'lastTransaction' => $customer->getLastTransaction(),
                'href' => $href,
            ];
        }

        $lang['add'] = new TranslatableMessage('add');
        $lang['edit'] = new TranslatableMessage('edit');
        $lang['customerHistory'] = new TranslatableMessage('customer.history');

        // Список сортировки
        $sorts = [];

        $sorts[] = [
            'text' => 'По клиентам (А - Я)',
            'sorting' => 'c.name',
            'order' => 'ASC',
            'href' => $this->adminUrlGenerator
                ->setRoute('user_customer', ['id' => $market->getId()])
                ->setAll($params)
                ->set('sorting', 'c.name')
                ->set('order', 'ASC')
                ->generateUrl(),
        ];

        $sorts[] = [
            'text' => 'По адресам (А - Я)',
            'sorting' => 'c.place',
            'order' => 'ASC',
            'href' => $this->adminUrlGenerator
                ->setRoute('user_customer', ['id' => $market->getId()])
                ->setAll($params)
                ->set('sorting', 'c.place')
                ->set('order', 'ASC')
                ->generateUrl(),
        ];

        $sorts[] = [
            'text' => 'По реализации',
            'sorting' => 'c.total',
            'order' => 'DESC',
            'href' => $this->adminUrlGenerator
                ->setRoute('user_customer', ['id' => $market->getId()])
                ->setAll($params)
                ->set('sorting', 'c.total')
                ->set('order', 'DESC')
                ->generateUrl(),
        ];

        $sorts[] = [
            'text' => 'По приходу',
            'sorting' => 'c.last_transaction',
            'order' => 'ASC',
            'href' => $this->adminUrlGenerator
                ->setRoute('user_customer', ['id' => $market->getId()])
                ->setAll($params)
                ->set('sorting', 'c.last_transaction')
                ->set('order', 'ASC')
                ->generateUrl(),
        ];

        $referer = $this->adminUrlGenerator->setRoute('user_customer', ['id' => $market->getId()])->generateUrl();

        return $this->render('user/customer/index.html.twig', [
            'referer' => $referer,
            'pagination' => $pagination,
            'lang' => $lang,
            'sorts' => $sorts,
            'market' => $market,
            'customers' => $data['customer'],
        ]);
    }
}

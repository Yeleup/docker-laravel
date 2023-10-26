<?php

namespace App\Controller;

use App\Controller\Admin\CustomerCrudController;
use App\Controller\Admin\CustomerOrderCrudController;
use App\Entity\Customer;
use App\Entity\Transaction;
use App\Entity\Type;
use App\Form\CustomerOrderType;
use App\Repository\TransactionRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/customer_order", name="customer_order")
 */
class CustomerOrderController extends AbstractController
{
    private $adminUrlGenerator;
    private $translator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, TranslatorInterface $translator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->translator = $translator;
    }

    /**
     * @Route("/{id}", name="_index", requirements={"id"="\d+"})
     */
    public function index(Request $request, Customer $customer, TransactionRepository $customerOrderRepository): Response
    {
        // GET
        $params = [];
        if ($request->query->get('market')) {
            $params['market'] = $request->query->get('market');
        }

        if ($request->query->get('search')) {
            $params['search'] = $request->query->get('search');
        }

        if ($request->query->get('order')) {
            $params['order'] = $request->query->get('order');
        }

        if ($request->query->get('sorting')) {
            $params['sorting'] = $request->query->get('sorting');
        }

        if ($request->query->get('page')) {
            $params['page'] = $request->query->get('page');
        }

        // Создаем GET параметры
        if (!$request->query->get('limit')) {
            $request->query->set('limit', 4);
        }

        // Text
        $lang['user'] = $this->translator->trans('customer_order.user');
        $lang['created'] = $this->translator->trans('customer_order.created');
        $lang['type'] = $this->translator->trans('customer_order.type');
        $lang['payment'] = $this->translator->trans('customer_order.payment');
        $lang['amount'] = $this->translator->trans('customer_order.amount');
        $lang['action'] = $this->translator->trans('customer_order.action');
        $lang['edit'] = $this->translator->trans('edit');
        $lang['delete'] = $this->translator->trans('delete');
        $lang['add'] = $this->translator->trans('add');
        $lang['back'] = $this->translator->trans('back');
        $lang['no_records_found'] = $this->translator->trans('no_records_found');

        $data['customer_orders'] = [];

        $customer_orders = $customerOrderRepository->findBy(['customer' => $customer], ['createdAt' => 'DESC'], $request->query->get('limit'));
        $customer_orders = array_reverse($customer_orders);

        foreach ($customer_orders as $customerOrder) {
            if ($customer->getMarket()) {
                $params['market'] = $customer->getMarket()->getId();
            }

            $edit = false;

            /*
             * Пользователь может изменять
             * если заказ добавлен пользователем
             * если дата заказа совпадает с текущей
             */
            if ($this->getUser() == $customerOrder->getUser() && $customerOrder->getCreatedAt() && date('Y-m-d') == $customerOrder->getCreatedAt()->format('Y-m-d')) {
                $edit = $this->adminUrlGenerator
                    ->setRoute('customer_order_edit', ['id' => $customerOrder->getId()])
                    ->setAll($params)
                    ->generateUrl();
            }

            $delete = $this->adminUrlGenerator
                ->setRoute('customer_order_delete', ['id' => $customerOrder->getId()])
                ->setAll($params)
                ->generateUrl();

            $data['customer_orders'][] = [
                'id' => $customerOrder->getId(),
                'user' => $customerOrder->getUser(),
                'updated' => $customerOrder->getUpdatedAt(),
                'type' => $customerOrder->getType(),
                'payment' => $customerOrder->getPayment(),
                'amount' => $customerOrder->getAmount(),
                'edit' => $edit,
                'delete' => $delete,
            ];
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $link['add'] = $this->adminUrlGenerator->setRoute('customer_order_new', ['id' => $customer->getId()])->generateUrl();
            $link['edit'] = $this->adminUrlGenerator->setController(CustomerCrudController::class)->setEntityId($customer->getId())->setAction(Action::EDIT)->generateUrl();
            $link['back'] = $this->adminUrlGenerator->setController(CustomerCrudController::class)->setAction('index')->generateUrl();
        } else {
            $link['add'] = $this->adminUrlGenerator->setRoute('customer_order_new', ['id' => $customer->getId()])
                ->setAll($params)
                ->generateUrl();

            $link['edit'] = $this->adminUrlGenerator->setController(CustomerCrudController::class)
                ->setEntityId($customer->getId())
                ->setAction(Action::EDIT)
                ->setAll($params)
                ->includeReferrer()
                ->generateUrl();

            $link['back'] = $this->adminUrlGenerator->setRoute('user_customer', ['id' => $request->query->get('market')])
                ->setAll($params)
                ->generateUrl();
        }

        $link['history'] = $this->adminUrlGenerator->setRoute('customer_order_history', ['id' => $customer->getId()])
            ->setAll($params)
            ->generateUrl();

        // Список сортировки
        $sorts = [];

        $sorts[] = [
            'text' => $this->translator->trans('default'),
            'limit' => $request->query->get('limit'),
            'href' => $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])
                ->setAll($params)
                ->generateUrl(),
        ];

        $sorts[] = [
            'text' => $this->translator->trans('customer.history'),
            'limit' => 1000,
            'href' => $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])
                ->setAll($params)
                ->set('limit', 1000)
                ->generateUrl(),
        ];

        if ($this->isGranted('ROLE_USER')) {
            $render = $this->render('user/customer_order/index.html.twig', [
                'sorts' => $sorts,
                'link' => $link,
                'customer' => $customer,
                'customer_orders' => $data['customer_orders'],
                'lang' => $lang,
            ]);
        } else {
            $render = $this->render('customer_order/index.html.twig', [
                'sorts' => $sorts,
                'link' => $link,
                'customer' => $customer,
                'customer_orders' => $data['customer_orders'],
                'lang' => $lang,
            ]);
        }

        return $render;
    }

    /**
     * @Route("/new/{id}", name="_new", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function new(Request $request, Customer $customer): Response
    {
        // GET
        $params = [];
        if ($customer->getMarket()) {
            $params['market'] = $customer->getMarket()->getId();
        }

        if ($request->query->get('search')) {
            $params['search'] = $request->query->get('search');
        }

        if ($request->query->get('order')) {
            $params['order'] = $request->query->get('order');
        }

        if ($request->query->get('sorting')) {
            $params['sorting'] = $request->query->get('sorting');
        }

        if ($request->query->get('page')) {
            $params['page'] = $request->query->get('page');
        }

        // Text
        $lang['create'] = $this->translator->trans('create');
        $lang['return'] = $this->translator->trans('return');

        $customerOrder = new Transaction();
        $customerOrder->setCustomer($customer);
        $customerOrder->setUser($this->getUser());
        $form = $this->createForm(CustomerOrderType::class, $customerOrder);

        $form->add('updatedAt', DateTimeType::class, [
            'label_format' => $this->translator->trans('customer_order.updated'),
            'widget' => 'single_text',
            'html5' => false,
            'format' => 'yyyy-MM-dd HH:mm:ss',
            'attr' => ['class' => 'js-datepicker', 'readonly' => true],
            'constraints' => [
                new NotBlank(['message' => 'Не должно быть пустым']),
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerOrder->setUser($this->getUser());

            // Добавляем заказ пользователя
            $customerOrder->setCustomer($customer);

            // Добавления реализации
            $this->getDoctrine()->getRepository(Transaction::class)->addOrder($customerOrder);

            if ($this->isGranted('ROLE_ADMIN')) {
                $redirect = $this->redirect($this->adminUrlGenerator->setRoute('customer_order_index', ['id'=> $customer->getId()])->generateUrl());
            } else {
                $redirect = $this->redirect(
                    $this->adminUrlGenerator
                        ->setRoute('customer_order_index', ['id'=> $customer->getId()])
                        ->setAll($params)
                        ->generateUrl()
                );
            }

            return $redirect;
        }

        $types = $this->getDoctrine()->getRepository(Type::class)->findAll();

        $data['types'] = [];

        foreach ($types as $type) {
            $data['types'][] = [
                'id' => $type->getId(),
                'payment_status' => $type->getPaymentStatus(),
            ];
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $link['return'] = $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])->generateUrl();
        } else {
            $link['return'] = $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])
                ->setAll($params)
                ->generateUrl();
        }

        return $this->render('customer_order/new.html.twig', [
            'link' => $link,
            'customer' => $customer,
            'data' => $data,
            'customer_order' => $customerOrder,
            'form' => $form->createView(),
            'lang' => $lang,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="_edit", requirements={"id"="\d+"}, methods={"GET","POST"})
     */
    public function edit(Request $request, Transaction $customerOrder): Response
    {
        $customer = $customerOrder->getCustomer();

        // GET
        $params = [];
        if ($customer->getMarket()) {
            $params['market'] = $customer->getMarket()->getId();
        }

        if ($request->query->get('search')) {
            $params['search'] = $request->query->get('search');
        }

        if ($request->query->get('order')) {
            $params['order'] = $request->query->get('order');
        }

        if ($request->query->get('sorting')) {
            $params['sorting'] = $request->query->get('sorting');
        }

        if ($request->query->get('page')) {
            $params['page'] = $request->query->get('page');
        }

        // Text
        $lang['save'] = $this->translator->trans('save');
        $lang['return'] = $this->translator->trans('return');

        $form = $this->createForm(CustomerOrderType::class, $customerOrder)->remove('updated');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customerOrder->setUser($this->getUser());

            // Добавляем заказ пользователя
            $customerOrder->setCustomer($customer);

            // Редактирование реализации
            $this->getDoctrine()->getRepository(Transaction::class)->editOrder($customerOrder);

            if ($this->isGranted('ROLE_ADMIN')) {
                $redirect = $this->redirect($this->adminUrlGenerator->setRoute('customer_order_index', ['id'=> $customer->getId()])->generateUrl());
            } else {
                $redirect = $this->redirect(
                    $this->adminUrlGenerator
                        ->setRoute('customer_order_index', ['id'=> $customer->getId()])
                        ->setAll($params)
                        ->generateUrl()
                );
            }

            return $redirect;
        }

        $types = $this->getDoctrine()->getRepository(Type::class)->findAll();

        $data['types'] = [];

        foreach ($types as $type) {
            $data['types'][] = [
               'id' => $type->getId(),
               'payment_status' => $type->getPaymentStatus(),
            ];
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $link['return'] = $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])->generateUrl();
        } else {
            $link['return'] = $this->adminUrlGenerator->setRoute('customer_order_index', ['id' => $customer->getId()])
                ->setAll($params)
                ->generateUrl();
        }

        return $this->render('customer_order/edit.html.twig', [
            'link' => $link,
            'customer' => $customer,
            'data' => $data,
            'customer_order' => $customerOrder,
            'form' => $form->createView(),
            'lang' => $lang,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Transaction $customerOrder): Response
    {
        $customer = $customerOrder->getCustomer();

        if ($this->isCsrfTokenValid('delete'.$customerOrder->getId(), $request->request->get('_token'))) {
            $this->getDoctrine()->getRepository(Transaction::class)->deleteOrder($customerOrder);
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            $return = $this->redirect($this->adminUrlGenerator->setRoute('customer_order_index', ['id'=> $customer->getId()])->generateUrl());
        } else {
            $return = $this->redirect($this->adminUrlGenerator->setRoute('customer_order_index', ['id'=> $customer->getId()])
                ->set('market', $customer->getMarket()->getId())
                ->set('search', $request->query->get('search'))
                ->set('order', $request->query->get('order'))
                ->set('sorting', $request->query->get('sorting'))
                ->set('page', $request->query->get('page'))
                ->generateUrl());
        }

        return $return;
    }
}

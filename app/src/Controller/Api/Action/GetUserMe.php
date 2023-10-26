<?php
namespace App\Controller\Api\Action;

use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Repository\TypeRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetUserMe extends AbstractController
{
    /**
     * @Route(name="api_get_current_user", path="/api/users/me", methods={"GET"},
     * defaults={
     *      "_api_resource_class"=User::class,
     *      "_api_collection_operation_name"="get_current_user"
     *     }
     * )
     */
    public function __invoke(): JsonResponse
    {
        $user = [
            'id' => $this->getUser()->getId(),
            'fullName' => $this->getUser()->getFullName() ?? '',
            'username' => $this->getUser()->getUsername(),
            'role' => $this->getUser()->getRoles(),
        ];

        // Виды оплаты
        if ($this->isGranted('ROLE_CONTROL')) {
            $payments = [];

            if (!empty($this->getUser()->getPayments())) {
                foreach ($this->getUser()->getPayments()->toArray() as $payment) {
                    $payments[] = [
                        'id' => $payment->getId(),
                        'title' => $payment->getTitle(),
                    ];
                }
            }

            $user['payments'] = $payments;
        }

        // Точки продаж
        if ($this->isGranted('ROLE_USER')) {
            $markets = [];

            if (!empty($this->getUser()->getMarkets())) {
                foreach ($this->getUser()->getMarkets()->toArray() as $market) {
                    $markets[] = [
                        'id' => $market->getId(),
                        'title' => $market->getTitle(),
                    ];
                }
            }

            $user['markets'] = $markets;
        }

        return new JsonResponse($user);
    }
}
<?php

namespace App\Controller\Api\Action;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\MarketRepository;
use App\Repository\PaymentRepository;
use App\Repository\TypeRepository;
use App\Service\MoneyFormatter;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GetTransactionStatistic
{
    private User $user;
    private TypeRepository $typeRepository;
    private MarketRepository $marketRepository;
    private PaymentRepository $paymentRepository;
    private MoneyFormatter $moneyFormatter;

    public function __construct(
        Security $security,
        TypeRepository $typeRepository,
        MarketRepository $marketRepository,
        PaymentRepository $paymentRepository,
        MoneyFormatter $moneyFormatter)
    {
        $this->typeRepository = $typeRepository;
        $this->marketRepository = $marketRepository;
        $this->moneyFormatter = $moneyFormatter;
        $this->paymentRepository = $paymentRepository;
        $this->user = $security->getUser();
    }

    /**
     * @Route(name="api_get_statistic", path="/api/transactions/statistic", methods={"GET"},
     * defaults={
     *      "_api_resource_class"=Transaction::class,
     *      "_api_collection_operation_name"="get_statistic"
     *     }
     * )
     */
    public function __invoke(Request $request): JsonResponse
    {
        $endDate = Carbon::createFromFormat('Y-m-d', $request->query->get('endDate'))->endOfDay();

        $markets = $this->marketRepository->createQueryBuilder('m')
            ->join('m.users', 'u')
            ->where('u.id = :user')
            ->setParameter('user', $this->user)
            ->getQuery()
            ->getResult()
        ;

        $data = [];
        foreach ($markets as $market) {
            $types = $this->typeRepository->createQueryBuilder('t')
                ->select('t.id', 't.title', 't.payment_status', 'SUM(transaction.amount) as value')
                ->join('t.transactions', 'transaction')
                ->join('transaction.customer', 'customer')
                ->join('customer.market', 'market')
                ->where('transaction.createdAt BETWEEN :startDate AND :endDate AND market.id = :market_id')
                ->groupBy('t.id')
                ->setParameter('startDate', $request->query->get('startDate'))
                ->setParameter('endDate', $endDate)
                ->setParameter('market_id', $market->getId())
                ->getQuery()
                ->getResult();

            $statistics = [];
            $value = 0;
            foreach ($types as $type) {
                $payments = $this->paymentRepository->createQueryBuilder('p')
                    ->select('p.id', 'p.title', 'SUM(transaction.amount) as value')
                    ->join('p.transactions', 'transaction')
                    ->join('transaction.type', 'type')
                    ->join('transaction.customer', 'customer')
                    ->join('customer.market', 'market')
                    ->where('type.id = :typeId AND transaction.createdAt BETWEEN :startDate AND :endDate AND market.id = :market_id')
                    ->setParameter('typeId', $type['id'])
                    ->setParameter('startDate', $request->query->get('startDate'))
                    ->setParameter('endDate', $endDate)
                    ->setParameter('market_id', $market->getId())
                    ->groupBy('p.id')
                    ->getQuery()
                    ->getResult();

                $statisticPayments = [];
                foreach ($payments as $payment) {
                    $statisticPayments[] = array(
                        'id' => $payment['id'],
                        'title' => $payment['title'],
                        'value' => $this->moneyFormatter->format($payment['value']),
                    );
                }

                $value += $type['value'];

                $statistics[] = array(
                    'id' => $type['id'],
                    'title' => $type['title'],
                    'value' => $this->moneyFormatter->format($type['value']),
                    'payments' => $statisticPayments
                );
            }

            if ($statistics) {
                $data[] = array(
                    'id' => $market->getId(),
                    'title' => $market->getTitle(),
                    'value' => $this->moneyFormatter->format($value),
                    'statistics' => $statistics,
                );
            }
        }

        return new JsonResponse($data);
    }
}
<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Transaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Transaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Transaction[]    findAll()
 * @method Transaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function sumAmountByCustomer(Customer $customer)
    {
        $qb = $this->createQueryBuilder('co');

        return $qb->select('SUM(co.amount)')
            ->where('co.customer = :customer')
            ->setParameter('customer', $customer)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function plusOrMinusDependingType(Transaction $transaction)
    {
        if ($transaction->getType()) {
            $amount = (float) abs($transaction->getAmount());

            // Плюсуем или минусуем, смотря по префиксу
            if ($transaction->getType()->getPrefix() == '-') {
                $amount = -1 * $amount;
                $transaction->setAmount($amount);
            } else {
                $transaction->setAmount($amount);
            }

            // Не показываем оплату если в типах не указано
            if (!$transaction->getType()->getPaymentStatus()) {
                $transaction->setPayment(null);
            }
        }

        return $transaction;
    }

    public function addOrder(Transaction $transaction)
    {
        $entityManager = $this->getEntityManager();
        $transaction = $this->plusOrMinusDependingType($transaction);
        $entityManager->persist($transaction);
        $entityManager->flush();
    }

    public function editOrder(Transaction $transaction)
    {
        $entityManager = $this->getEntityManager();
        $transaction = $this->plusOrMinusDependingType($transaction);
        $entityManager->persist($transaction);
        $entityManager->flush();
    }

    public function deleteOrder(Transaction $transaction)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($transaction);
        $entityManager->flush();
    }
}

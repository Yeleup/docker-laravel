<?php

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function Symfony\Component\String\u;

/**
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function updateCustomerTotalAndLastTransaction(Customer $customer)
    {
        // Assuming you have a sumAmountByCustomer method in your Transaction repository
        $total = $this->_em->getRepository(Transaction::class)->sumAmountByCustomer($customer);

        $customer->setTotal($total);
        $customer->setLastTransaction(new \DateTime('now'));

        $this->_em->persist($customer);
        $this->_em->flush();
    }

    public function findByFilter(array $filter): array
    {
        $search = '';

        if (isset($filter['search'])) {
            $search = $filter['search'];
        }

        $searchTerms = $this->extractSearchTerms($search);

        $sort = 'c.last_transaction';

        $sort_data = [
            'c.last_transaction',
            'c.total',
            'c.place',
            'c.name',
        ];

        if (isset($filter['sort']) && in_array($filter['sort'], $sort_data)) {
            $sort = $filter['sort'];
        }

        if (isset($filter['order']) && ($filter['order'] == 'DESC')) {
            $order = 'DESC';
        } else {
            $order = 'ASC';
        }

        $queryBuilder = $this->createQueryBuilder('c');

        $queryBuilder
            ->andWhere('c.market = :market')
            ->setParameter('market', $filter['market'])
        ;

        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->andWhere('c.name LIKE :t_'.$key)
                ->setParameter('t_'.$key, '%'.$term.'%')
            ;
        }

        return $queryBuilder
            ->orderBy($sort, $order)
            ->getQuery()
            ->getResult();
    }

    private function extractSearchTerms(string $searchQuery): array
    {
        $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
        $terms = array_unique($searchQuery->split(' '));

        // ignore the search terms that are too short
        return array_filter($terms, function ($term) {
            return 2 <= $term->length();
        });
    }

    // /**
    //  * @return Customer[] Returns an array of Customer objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Customer
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

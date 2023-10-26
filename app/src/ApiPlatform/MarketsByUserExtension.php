<?php

namespace App\ApiPlatform;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Customer;
use App\Entity\Market;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Security;

class MarketsByUserExtension implements QueryCollectionExtensionInterface
{
    private Security $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ($resourceClass !== Market::class) {
            return;
        }

        $alias = $queryBuilder->getRootAliases()[0];

        if ($this->security->getUser()) {
            $queryBuilder
                ->join(sprintf('%s.users', $alias), 'u')
                ->where('u = :user')
                ->setParameter('user', $this->security->getUser());
        }
    }
}
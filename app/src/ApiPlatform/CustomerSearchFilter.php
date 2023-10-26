<?php

namespace App\ApiPlatform;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

class CustomerSearchFilter extends AbstractFilter
{

    protected function filterProperty(string $property, $value, QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if ('search' !== $property || empty($value)) {
            return;
        }

        // Алиас для вашего основного объекта (например, "o" для "object")
        $alias = $queryBuilder->getRootAliases()[0];

        // Добавьте ваш кастомный запрос к QueryBuilder
        $queryBuilder
            ->andWhere(sprintf('%s.name LIKE :name', $alias))
            ->orWhere(sprintf('%s.place LIKE :place', $alias))
            ->setParameter('name', $value . '%')
            ->setParameter('place', $value . '%')
        ;
    }

    public function getDescription(string $resourceClass): array
    {
        $description = [];
        foreach ($this->properties as $property => $strategy) {
            $description["$property"] = [
                'property' => $property,
                'type' => Type::BUILTIN_TYPE_STRING,
                'required' => false,
                'description' => 'Filter with strategy: '.$strategy,
            ];
        }
        return $description;
    }
}
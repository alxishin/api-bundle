<?php

declare(strict_types=1);

namespace ApiBundle\Dto\Request;

use ApiBundle\Enum\OrderEnum;
use Doctrine\ORM\QueryBuilder;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

readonly class BaseRequest implements RequestInterface
{
    public const int DEFAULT_LIMIT = 100;

    public function __construct(
        #[Assert\GreaterThanOrEqual(1)]
        public int $page = 1,
        #[Assert\GreaterThanOrEqual(1)]
        #[Assert\LessThanOrEqual(10000)]
        public int $limit = self::DEFAULT_LIMIT,

        #[OA\Property(
            property: 'sortBy',
            description: 'Сортировка по полям'
        )]
        public ?string $sortBy = null,
        public ?OrderEnum $order = null,

        #[OA\Property(
            description: 'Фильтрация по значениям, равным переданному значению. Например eq[date]=2000-01-01',
        )]
        public array $eq = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, не равным переданному значению. Например neq[date]=2000-01-01',
        )]
        public array $neq = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, меньшим, либо равным переданному значению. Например lte[date]=2000-01-01',
        )]
        public array $lte = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, большим, либо равным переданному значению. Например gte[date]=2000-01-01',
        )]
        public array $gte = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, большим переданному значению. Например gt[date]=2000-01-01',
        )]
        public array $gt = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, меньшим переданному значению. Например lt[date]=2000-01-01',
        )]
        public array $lt = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, равным null. Например null[date]=1',
        )]
        public array $null = [],

        #[OA\Property(
            description: 'Фильтрация по значениям, меньшим переданному значению. Например notnull[date]=1',
        )]
        public array $notnull = [],
    )
    {
    }

    public function getItemsAndTotalCount(QueryBuilder $queryBuilder, int $limit, int $offset): array
    {
        $i = 0;
        foreach ($this->eq as $field => $item) {
            $this->handleFilter($queryBuilder, 't.'.$field, $item, '=', $i);
            $i++;
        }

        foreach ($this->neq as $field => $item) {
            $this->handleFilter($queryBuilder, 't.'.$field, $item, '!=', $i);
            $i++;
        }

        foreach ($this->gte as $field => $item) {
            $this->handleFilter($queryBuilder, 't.'.$field, $item, '>=', $i);
            $i++;
        }

        foreach ($this->lte as $field => $item) {
            $this->handleFilter($queryBuilder, 't.'.$field, $item, '<=', $i);
            $i++;
        }

        foreach ($this->lt as $field => $item) {
            $this->handleFilter($queryBuilder, 't.'.$field, $item, '<', $i);
            $i++;
        }

        foreach ($this->gt as $field => $item) {
            $this->handleFilter($queryBuilder, 't.'.$field, $item, '>', $i);
            $i++;
        }

        foreach ($this->null as $field => $item) {
            $queryBuilder->andWhere(sprintf('%s is null', 't.'.$field));
        }

        foreach ($this->notnull as $field => $item) {
            $queryBuilder->andWhere(sprintf('%s is not null', 't.'.$field));
        }

        if ($this->sortBy && $this->order) {
            $queryBuilder
                ->addOrderBy(sprintf('t.%s', $this->sortBy), $this->order->value);

        }
        return [
            $queryBuilder
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery()
                ->getResult(),
            $queryBuilder
                ->select('count(t) as count')
                ->setMaxResults(null)
                ->setFirstResult(0)
                ->resetDQLPart('orderBy')
                ->getQuery()
                ->getSingleScalarResult()];
    }

    private function handleFilter(QueryBuilder $queryBuilder, string $field, string $value, string $compareType, int $i): void
    {
        $bind = sprintf('param_%s', $i);
        $queryBuilder
            ->andWhere(sprintf('%s %s :%s', $field, $compareType, $bind))
            ->setParameter($bind, $value);
    }

    #[Ignore]
    public function getOrderBy(): array
    {
        if (is_null($this->order)) {
            return [];
        }
        return [
            $this->sortBy => $this->order->value,
        ];
    }
}

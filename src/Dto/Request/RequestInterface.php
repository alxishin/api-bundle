<?php

declare(strict_types=1);

namespace ApiBundle\Dto\Request;

use Doctrine\ORM\QueryBuilder;

interface RequestInterface
{
    public function getItemsAndTotalCount(QueryBuilder $queryBuilder, int $limit, int $offset): array;
}

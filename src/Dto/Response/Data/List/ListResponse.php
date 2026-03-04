<?php

declare(strict_types=1);

namespace ApiBundle\Dto\Response\Data\List;

use OpenApi\Attributes as OA;

readonly class ListResponse
{
    public function __construct(
        #[OA\Property(description: 'Массив элементов данных')]
        private array $items,
        private int $limit,
        private int $page,
        private int $total)
    {
    }

    public function toArray(): array
    {
        return [
            'items' => $this->items,
            'limit' => $this->limit,
            'page' => $this->page,
            'total' => $this->total
        ];
    }
}

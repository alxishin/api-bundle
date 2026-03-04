<?php

declare(strict_types=1);

namespace ApiBundle\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class CollectionDto
{
    public function __construct(
        #[Assert\Valid()]
        private array $items
    )
    {
    }

    public function getItems()
    {
        return $this->items;
    }
}

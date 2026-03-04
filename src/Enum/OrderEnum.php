<?php

declare(strict_types=1);

namespace ApiBundle\Enum;

enum OrderEnum: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }
}

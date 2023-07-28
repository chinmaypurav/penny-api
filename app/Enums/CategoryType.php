<?php

namespace App\Enums;

enum CategoryType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}

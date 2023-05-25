<?php

namespace App\Enums;

enum CategoryType: string
{
    case INCOME = 'Income';
    case EXPENSE = 'Expense';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}

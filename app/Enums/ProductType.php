<?php

namespace App\Enums;

enum ProductType: string
{
    case Normal = 'normal';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'منتج عادي',
            self::Custom => 'منتج مخصص',
        };
    }
}

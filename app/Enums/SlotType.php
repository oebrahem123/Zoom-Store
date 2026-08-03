<?php

namespace App\Enums;

enum SlotType: string
{
    case Main = 'main';
    case Logo = 'logo';
    case Secondary = 'secondary';
    case Accessory = 'accessory';

    public function label(): string
    {
        return match ($this) {
            self::Main => 'المنطقة الرئيسية',
            self::Logo => 'شعار',
            self::Secondary => 'منطقة ثانوية',
            self::Accessory => 'ملحق',
        };
    }
}

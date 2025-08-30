<?php

namespace App\Helpers;
class OrderStatus
{
    public const PENDING = 'PENDING'; //bekliyor
    public const CONFIRMED = 'CONFIRMED'; //ONAYLANDI
    public const PREPARED = 'PREPARED'; //hazırlandı
    public const ASSIGNED = 'ASSIGNED';  //kuryeye atandı
    public const PICKUP = 'PICKUP';  //YOLDA OLANLAR
    public const HANDOVER = 'HANDOVER';  //kuryeye verildi
    public const DELIVERED = 'DELIVERED'; // teslim edildi
    public const UNSUPPLIED = 'UNSUPPLIED'; //reddedildi

    public static function all(): array
    {
        return [
            self::PENDING,
            self::PREPARED,
            self::CONFIRMED,
            self::ASSIGNED,
            self::PICKUP,
            self::HANDOVER,
            self::DELIVERED,
            self::UNSUPPLIED,
        ];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::all(), true);
    }
}

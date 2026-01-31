<?php

namespace App\Helpers;
class OrderStatus
{
    public const PENDING = 'PENDING'; //BEKLEİYOR
    public const CONFIRMED = 'CONFIRMED'; //ONAYLANDI
    public const PREPARING = 'PREPARING'; //HAZIRLANIYOR
    public const PREPARED = 'PREPARED'; //HAZIRLANDI
    public const ASSIGNED = 'ASSIGNED';  // KURYEE ATANDI
    public const PICKUP = 'PICKUP';  // YOLA ÇIKTI
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

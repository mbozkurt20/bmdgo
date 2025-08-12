<?php

namespace App\Helpers;
class CourierStatus
{
    public const active = 'active'; // bekliyor
    public const passive = 'passive'; // siparişe kapalı
    public const break = 'break'; //molada
    public const service = 'service'; //servista

    public static function all(): array
    {
        return [
            self::active,
            self::passive,
            self::break,
            self::service,
        ];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::all(), true);
    }
}

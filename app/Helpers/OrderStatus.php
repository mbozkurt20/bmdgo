<?php

namespace App\Helpers;
class OrderStatus
{
    public const UNSUPPLIED = 'UNSUPPLIED';
    public const HANDOVER = 'HANDOVER';
    public const DELIVERED = 'DELIVERED';
    public const PREPARED = 'PREPARED';
    public const PENDING = 'PENDING';

    public static function all(): array
    {
        return [
            self::UNSUPPLIED,
            self::HANDOVER,
            self::DELIVERED,
            self::PENDING,
            self::PREPARED,
        ];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::all(), true);
    }
}

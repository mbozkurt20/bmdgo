<?php

namespace App\Helpers;
class OrderStatus
{
    public const PENDING = 'PENDING'; //BEKLİYOR 5 SN SONRA HAZIRLANIYOR
    public const PREPARED = 'PREPARED'; //HAZIRLANDI
    public const ASSIGNED = 'ASSIGNED';  // kAbUL EDİLDİ
    public const HANDOVER = 'HANDOVER';  // teslim al
    public const DELIVERED = 'DELIVERED'; // teslim edildi
    public const UNSUPPLIED = 'UNSUPPLIED'; //reddedildi

    public static function all(): array
    {
        return [
            self::PENDING,
            self::PREPARED,
            self::ASSIGNED,
            self::HANDOVER,
            self::DELIVERED,
            self::UNSUPPLIED,
        ];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::all(), true);
    }

    public static function statuses()
    {
        return [
            'PENDING' => 'PENDING',
            'PREPARED' => 'PREPARED',
            'ASSIGNED' => 'ASSIGNED',
            'HANDOVER' => 'HANDOVER',
            'DELIVERED' => 'DELIVERED',
            'UNSUPPLIED' => 'UNSUPPLIED'
        ];
    }
}

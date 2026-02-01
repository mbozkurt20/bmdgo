<?php

namespace App\Helpers;
class OrderStatus
{
    public const PENDING = 'PENDING'; //BEKLEİYOR
    public const PREPARING = 'PREPARING'; //HAZIRLANIYOR
    public const PREPARED = 'PREPARED'; //HAZIRLANDI
    public const PENDING_ASSIGNED = 'PENDING_ASSIGNED';  // KURYEE ATAMA AŞAMASINDA
    public const ASSIGNED = 'ASSIGNED';  // KURYEE ATANDI
    public const ACCEPT_ASSIGNED = 'ACCEPT_ASSIGNED';  // KURYEE TESLİM ALDI

    public const HANDOVER = 'HANDOVER';  // YOLA ÇIKTI
    public const DELIVERED = 'DELIVERED'; // teslim edildi
    public const UNSUPPLIED = 'UNSUPPLIED'; //reddedildi

    public static function all(): array
    {
        return [
            self::PENDING,
            self::PREPARING,
            self::PREPARED,
            self::PENDING_ASSIGNED,
            self::ASSIGNED,
            self::ACCEPT_ASSIGNED,
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
            'PENDING' => 'pending',
            'PREPARING' => 'preparing',
            'PREPARED' => 'prepared',
            'PENDING_ASSIGNED' => 'pending_assigned',
            'ASSIGNED' => 'assigned',
            'ACCEPT_ASSIGNED' => 'accept_assigned',
            'HANDOVER' => 'handover',
            'DELIVERED' => 'delivered',
            'UNSUPPLIED' => 'unsupplied'
        ];
    }
}

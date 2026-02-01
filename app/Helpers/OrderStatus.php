<?php

namespace App\Helpers;
class OrderStatus
{
    /*
     * Sipariş Durumları
     *
     */
    public const PENDING = 'PENDING'; //BEKLİYOR 5 SN SONRA HAZIRLANIYOR
    //public const PREPARING = 'PREPARING'; //HAZIRLANIYOR 3 dk sonra
    public const PREPARED = 'PREPARED'; //HAZIRLANDI
    //public const PENDING_ASSIGNED = 'PENDING_ASSIGNED';  // KURYE ATAMA AŞAMASINDA
    public const ASSIGNED = 'ASSIGNED';  // kAbUL EDİLDİ
    //public const ACCEPT_ASSIGNED = 'ACCEPT_ASSIGNED';  // KURYE TESLİM ALDI

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
            'PENDING' => 'pending',
            'PREPARED' => 'prepared',
            'ASSIGNED' => 'assigned',
            'ACCEPT_ASSIGNED' => 'accept_assigned',
            'HANDOVER' => 'handover',
            'DELIVERED' => 'delivered',
            'UNSUPPLIED' => 'unsupplied'
        ];
    }
}

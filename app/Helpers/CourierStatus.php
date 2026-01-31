<?php

namespace App\Helpers;
use App\Models\CourierOrder;

class CourierStatus
{
    public const active = 'active'; // bekliyor
    public const passive = 'passive'; // siparişe kapalı
    public const break = 'break'; //molada
    public const service = 'service'; //servista
    public const handover = 'handover'; //servista

    public static function all(): array
    {
        return [
            self::active,
            self::passive,
            self::break,
            self::service,
            self::handover,
        ];
    }

    public static function isValid(string $value): bool
    {
        return in_array($value, self::all(), true);
    }

    public static function calculatePrice($courier,$totalAmount){
        if($courier->price_type == 'package'){

        }else{

        }
    }
}

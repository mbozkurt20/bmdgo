<?php

namespace App\Helpers;

use App\Enums\EntegraStatusEnum;
use ReflectionClass;

class EntegraStatusHelper
{
    /**
     * Rakamı ver, İSMİ al (Örn: 400 -> "PENDING")
     */
    public static function getNameByValue($value): string
    {
        $val = (int) $value;

        if ($val === 1500 || $val === 1600) {
            return 'UNSUPPLIED';
        }

        $reflect = new \ReflectionClass(EntegraStatusEnum::class);
        $constants = $reflect->getConstants();

        $name = array_search($val, $constants);

        return $name ?: 'UNKNOWN';
    }

    /**
     * İsim ver, RAKAMI al (Örn: "PENDING" -> 400)
     */
    public static function getValueByName(string $name): int
    {
        $reflect = new ReflectionClass(EntegraStatusEnum::class);
        $constants = $reflect->getConstants();
        $name = strtoupper($name);

        return $constants[$name] ?? 0;
    }
}

<?php

namespace App\Traits;

trait HasRandomCode
{
    /**
     * Boot the trait.
     */
    public static function bootHasRandomCode()
    {
        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = self::generateRandomCode();
            }
        });
    }

    /**
     * 6 haneli rastgele integer kod üretir.
     *
     * @return int
     */
    protected static function generateRandomCode(): int
    {
        return random_int(10000000, 99999999); // 6 haneli integer
    }
}

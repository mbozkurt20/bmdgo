<?php

namespace App\Helpers;

class  General
{
    static function phone($phone)
    {
        return preg_replace('/\D/', '', $phone);
    }
}

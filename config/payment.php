<?php

return [
    'paytr' => [
        'merchant_id'   => env('PAYTR_MERCHANT_ID'),
        'merchant_key'  => env('PAYTR_MERCHANT_KEY'),
        'merchant_salt' => env('PAYTR_MERCHANT_SALT'),
        'sandbox'       => env('PAYTR_SANDBOX', false),
    ],
];


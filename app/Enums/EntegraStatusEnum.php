<?php

namespace App\Enums;

class EntegraStatusEnum
{
    public const PENDING      = 400;
    public const PREPARING    = 500;
    public const HANDOVER     = 700;
    public const DELIVERED    = 900;
    public const UNSUPPLIED   = 1600;
    public const OUT_OF_STOCK = 1500;
}

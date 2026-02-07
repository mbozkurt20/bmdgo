<?php

namespace App\Enums;

enum EntegraStatusEnum: int
{
    case PENDING   = 400;
    case PREPARING = 500;
    case HANDOVER  = 700;
    case DELIVERED = 900;
}

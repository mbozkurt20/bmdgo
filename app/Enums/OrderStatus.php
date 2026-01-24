<?php

namespace App\Enums;

enum OrderStatus: int
{
    case Pending = 1;
    case Accepted = 2;
    case Packaged = 3;
    case Dispatched = 4;
    case Canceled = 5; //iptal edilen
    case Delivered = 6;
}

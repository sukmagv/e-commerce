<?php

namespace App\Modules\Order\Enums;

enum OrderStatus: string
{
    case PENDING     = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DECLINED    = 'declined';
    case FINISHED    = 'finished';
    case CANCELED    = 'canceled';
}

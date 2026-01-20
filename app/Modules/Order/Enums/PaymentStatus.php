<?php

namespace App\Modules\Order\Enums;

enum PaymentStatus: string
{
    case PENDING  = 'pending';
    case ACCEPTED = 'accepted';
    case DECLINED = 'declined';
}

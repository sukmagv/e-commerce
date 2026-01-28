<?php

namespace App\Modules\Order\Enums;

enum PaymentType: string
{
    case PDF   = 'pdf';
    case PHOTO = 'photo';
}

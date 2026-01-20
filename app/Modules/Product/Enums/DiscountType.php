<?php

namespace App\Modules\Product\Enums;

enum DiscountType: string
{
    case NOMINAL = 'nominal';
    case PERCENTAGE = 'percentage';
}

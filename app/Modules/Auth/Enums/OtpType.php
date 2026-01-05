<?php

namespace App\Modules\Auth\Enums;

enum OtpType: int
{
    case EMAIL = 1;
    case PHONE = 2;
}

<?php

namespace App\Modules\Auth\Enums;

enum OtpType: string
{
    case EMAIL = 'email';
    case PHONE = 'phone';
}

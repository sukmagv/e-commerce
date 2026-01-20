<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use App\Modules\Auth\Enums\OtpType;

class SendOtpDTO extends BaseDTO
{
    public ?int $otp_id;
    public string $address;
    public OtpType $type;
}

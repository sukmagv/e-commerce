<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;

class ForgotPasswordDTO extends BaseDTO
{
    public ?int $otp_id;
    public string $address;
}

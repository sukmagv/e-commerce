<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use App\Modules\Auth\Enums\OtpType;

class SendOtpDTO extends BaseDTO
{
    public function __construct(
        public ?int $otpId,
        public string $address,
        public OtpType $type,
    ) {}
}

<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;

class ResetPasswordDTO extends BaseDTO
{
    public function __construct(
        public int $otpId,
        public string $password,
    ) {}
}

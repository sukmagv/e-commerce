<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;

class ResetPasswordDTO
{
    public int $otp_id;
    public string $password;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->otp_id = $request->input('otp_id');
        $dto->password = $request->input('password');

        return $dto;
    }
}

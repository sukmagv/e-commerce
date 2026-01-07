<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;

class ForgotPasswordDTO
{
    public ?int $otp_id;
    public string $address;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->otp_id = $request->input('otp_id');
        $dto->address = $request->input('address');

        return $dto;
    }
}

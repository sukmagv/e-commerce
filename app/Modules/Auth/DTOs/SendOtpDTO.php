<?php

namespace App\Modules\Auth\DTOs;

use App\Modules\Auth\Enums\OtpType;
use Illuminate\Http\Request;

class SendOtpDTO
{
    public ?int $otp_id;
    public string $address;
    public OtpType $type;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->otp_id = $request->input('otp_id');
        $dto->address = $request->input('address');
        $dto->type = OtpType::from($request->input('type'));

        return $dto;
    }
}

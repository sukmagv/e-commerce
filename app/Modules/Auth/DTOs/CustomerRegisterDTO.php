<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CustomerRegisterDTO
{
    public int $otp_id;
    public ?string $code;
    public string $email;
    public string $name;
    public string $phone;
    public ?UploadedFile $photo;
    public string $password;
    public bool $is_blocked = false;

    /**
     * Create DTO instance
     * Maps input data from the request (form-data or JSON)
     *
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->otp_id = $request->input('otp_id');
        $dto->email = $request->input('email');
        $dto->name = $request->input('name');
        $dto->phone = $request->input('phone');
        $dto->password = $request->input('password');
        $dto->photo = $request->file('photo');
        $dto->code = $request->input('code');
        $dto->is_blocked = false;

        return $dto;
    }
}

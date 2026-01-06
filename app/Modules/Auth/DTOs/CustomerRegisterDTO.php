<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CustomerRegisterDTO
{
    public string $email;
    public string $name;
    public ?string $phone;
    public ?UploadedFile $photo;
    public string $password;

    /**
     * Create DTO instance
     * Maps input data from the request (form-data or JSON)
     *
     * @param \Illuminate\Http\Request $request
     * @return self
     */
    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->email = $request->input('email');
        $dto->name = $request->input('name');
        $dto->phone = $request->input('phone');
        $dto->password = $request->input('password');
        $dto->photo = $request->file('photo');

        return $dto;
    }
}

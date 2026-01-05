<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;

class CustomerLoginDTO
{
    public string $email;
    public string $password;

    public static function fromRequest(Request $request): self
    {
        $dto = new self();
        $dto->email = $request->input('email');
        $dto->password = $request->input('password');

        return $dto;
    }
}

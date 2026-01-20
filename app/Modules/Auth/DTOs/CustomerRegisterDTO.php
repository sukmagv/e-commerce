<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CustomerRegisterDTO extends BaseDTO
{
    public string $email;
    public string $name;
    public ?string $phone;
    public ?UploadedFile $photo;
    public string $password;

    public function toCustomerData(): array
    {
        return [
            'phone' => $this->phone,
        ];
    }
}

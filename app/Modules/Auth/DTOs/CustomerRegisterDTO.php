<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class CustomerRegisterDTO extends BaseDTO
{
    public function __construct(
        public string $email,
        public string $name,
        public ?string $phone,
        public ?UploadedFile $photo,
        public string $password,
    ) {}

    /**
     * Set user data
     *
     * @return array
     */
    public function toUserData(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'password' => $this->password,
        ];
    }

    /**
     * Set customer data
     *
     * @return array
     */
    public function toCustomerData(): array
    {
        return [
            'phone' => $this->phone,
        ];
    }
}

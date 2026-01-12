<?php

namespace App\Modules\Auth\DTOs;

use Illuminate\Http\Request;

class ChangePasswordDTO
{
    public string $currentPassword;
    public string $newPassword;

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
        $dto->currentPassword = $request->input('current_password');
        $dto->newPassword = $request->input('new_password');

        return $dto;
    }
}

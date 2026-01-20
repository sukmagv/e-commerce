<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;

class ChangePasswordDTO extends BaseDTO
{
    public string $currentPassword;
    public string $newPassword;
}

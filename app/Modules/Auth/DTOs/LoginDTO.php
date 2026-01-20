<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\Request;

class LoginDTO extends BaseDTO
{
    public string $email;
    public string $password;
}

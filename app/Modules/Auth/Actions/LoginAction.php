<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use App\Modules\Auth\DTOs\LoginDTO;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\Models\Customer;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Check email and password for login
     *
     * @param \App\Modules\Auth\DTOs\CustomerLoginDTO $dto
     * @return \App\Modules\Auth\Models\User
     */
    public function execute(LoginDTO $dto): User
    {
        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages(['message' => 'Email or password is incorrect']);
        }

        if ($user->isBlocked()) {
            throw ValidationException::withMessages(['message' => 'Account is blocked']);
        }

        $user->token = $user->generateToken();

        return $user;
    }
}

<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\DTOs\CustomerLoginDTO;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginAction
{
    /**
     * Check email and password for login
     *
     * @param CustomerLoginDTO $dto
     * @return User
     */
    public function execute(CustomerLoginDTO $dto): User
    {
        $user = User::query()
            ->where('email', $dto->email)
            ->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'Email or password is incorrect'
                ], Response::HTTP_UNAUTHORIZED)
            );
        }

        return $user;
    }
}

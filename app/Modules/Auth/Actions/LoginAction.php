<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\DTOs\CustomerLoginDTO;
use App\Modules\Auth\Models\Customer;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginAction
{
    /**
     * Check email and password for login
     *
     * @param \App\Modules\Auth\DTOs\CustomerLoginDTO $dto
     * @return array
     */
    public function execute(CustomerLoginDTO $dto): array
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

        $token = $user->generateToken();

        $customer = Customer::query()
            ->where('user_id', $user->id)
            ->first();

        $data = array_merge(
            $user->only(['id', 'name', 'email']),
            $customer->only(['phone', 'photo']),
        );

        return [
            'data' => $data,
            'token' => $token,
        ];
    }
}

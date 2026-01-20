<?php

namespace App\Http\Controllers\Admin\v1;

use App\Modules\Auth\DTOs\LoginDTO;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\LoginAction;
use App\Http\Requests\Customer\v1\LoginRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    /**
     * Admin login and generate access token
     *
     * @param \App\Http\Requests\Customer\v1\LoginRequest $request
     * @param \App\Modules\Auth\Actions\LoginAction $action
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function login(Request $request, LoginAction $action): JsonResource
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:6']
        ]);
        
        $dto = LoginDTO::fromRequest($request);

        $user = $action->execute($dto);

        return new JsonResource([
            'username' => $user->name,
            'token'    => $user->token,
        ]);
    }
}

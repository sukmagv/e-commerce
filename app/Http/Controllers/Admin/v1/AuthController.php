<?php

namespace App\Http\Controllers\Admin\v1;

use Illuminate\Http\Request;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\v1\LoginRequest;
use App\Modules\Auth\Actions\LoginAction;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    /**
     * Admin login and generate access token
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Modules\Auth\Actions\LoginAction $action
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResource
    {
        $user = $action->execute($request->payload());

        return new JsonResource([
            'username' => $user->name,
            'token'    => $user->token,
        ]);
    }
}

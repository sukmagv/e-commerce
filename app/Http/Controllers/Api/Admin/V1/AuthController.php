<?php

namespace App\Http\Controllers\Api\Admin\v1;

use App\Http\Controllers\Controller;
use App\Modules\Auth\Actions\LoginAction;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Requests\Api\Customer\V1\LoginRequest;

class AuthController extends Controller
{
    /**
     * Admin login and generate access token
     *
     * @param \App\Http\Requests\Api\Customer\V1\LoginRequest $request
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

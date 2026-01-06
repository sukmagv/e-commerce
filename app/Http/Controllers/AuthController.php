<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Modules\Auth\Actions\LoginAction;
use App\Modules\Auth\DTOs\CustomerLoginDTO;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthController extends Controller
{
    /**
     * Create user and customer data
     *
     * @param \App\Http\Requests\RegisterRequest $request
     * @param \App\Modules\Auth\Actions\RegisterAction $action
     * @return JsonResource
     */
    public function register(RegisterRequest $request, RegisterAction $action): JsonResource
    {
        $dto = CustomerRegisterDTO::fromRequest($request);

        $customer = $action->execute($dto);

        return JsonResource::make($customer);

    }

    /**
     * Customer login and generate access token
     *
     * @param \App\Http\Requests\LoginRequest $request
     * @param \App\Modules\Auth\Actions\LoginAction $action
     * @return JsonResource
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResource
    {
        $dto = CustomerLoginDTO::fromRequest($request);

        $user = $action->execute($dto);

        return JsonResource::make($user);
    }

    /**
     * Customer logout
     *
     * @param \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse();
    }
}

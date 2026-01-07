<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\CustomerResource;
use App\Modules\Auth\Actions\LoginAction;
use App\Modules\Auth\DTOs\CustomerLoginDTO;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class AuthController extends Controller
{
    /**
     * Create user and customer data
     *
     * @param \App\Http\Requests\RegisterRequest $request
     * @param \App\Modules\Auth\Actions\RegisterAction $action
     * @return \App\Http\Resources\CustomerResource
     */
    public function register(RegisterRequest $request, RegisterAction $action): CustomerResource
    {
        $dto = CustomerRegisterDTO::fromRequest($request);

        $customerData = $action->execute($dto);

        return new CustomerResource($customerData);
    }

    /**
     * Customer login and generate access token
     *
     * @param \App\Http\Requests\LoginRequest $request
     * @param \App\Modules\Auth\Actions\LoginAction $action
     * @return \App\Http\Resources\CustomerResource
     */
    public function login(LoginRequest $request, LoginAction $action): CustomerResource
    {
        $dto = CustomerLoginDTO::fromRequest($request);

        $customerData = $action->execute($dto);

        return new CustomerResource($customerData);
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

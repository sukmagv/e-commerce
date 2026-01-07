<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\CustomerResource;
use App\Modules\Auth\Actions\LoginAction;
use App\Http\Requests\ResetPasswordRequest;
use App\Modules\Auth\DTOs\CustomerLoginDTO;
use App\Modules\Auth\DTOs\ResetPasswordDTO;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\DTOs\ForgotPasswordDTO;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;
use App\Modules\Auth\Actions\ResetPasswordAction;
use App\Modules\Auth\Actions\ForgotPasswordAction;

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


    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action)
    {
        $dto = ForgotPasswordDTO::fromRequest($request);

        $action->execute($dto);

        return new JsonResponse();
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action)
    {
        $dto = ResetPasswordDTO::fromRequest($request);

        $action->execute($dto);

        return new JsonResponse();
    }
}

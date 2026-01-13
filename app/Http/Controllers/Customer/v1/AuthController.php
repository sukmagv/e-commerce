<?php

namespace App\Http\Controllers\Customer\v1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\v1\LoginRequest;
use App\Http\Requests\Customer\v1\RegisterRequest;
use App\Modules\Auth\Actions\LoginAction;
use App\Http\Resources\CustomerResource;
use App\Modules\Auth\DTOs\CustomerLoginDTO;
use App\Modules\Auth\DTOs\ResetPasswordDTO;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\DTOs\ForgotPasswordDTO;
use App\Http\Requests\Customer\v1\ResetPasswordRequest;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;
use App\Http\Requests\Customer\v1\ForgotPasswordRequest;
use App\Modules\Auth\Actions\ResetPasswordAction;
use App\Modules\Auth\Actions\ForgotPasswordAction;

class AuthController extends Controller
{
    /**
     * Create user and customer data
     *
     * @param \App\Http\Requests\Customer\v1\RegisterRequest $request
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
     * @param \App\Http\Requests\Customer\v1\LoginRequest $request
     * @param \App\Modules\Auth\Actions\LoginAction $action
     * @return \App\Http\Resources\CustomerResource
     */
    public function login(LoginRequest $request, LoginAction $action): CustomerResource
    {
        $dto = CustomerLoginDTO::fromRequest($request);

        return new CustomerResource($action->execute($dto));
    }

    /**
     * Customer logout
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return new JsonResponse();
    }

    /**
     * Send OTP for forgot password request
     *
     * @param \App\Http\Requests\Customer\v1\ForgotPasswordRequest $request
     * @param \App\Modules\Auth\Actions\ForgotPasswordAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action): JsonResponse
    {
        $dto = ForgotPasswordDTO::fromRequest($request);

        $action->execute($dto);

        return new JsonResponse();
    }

    /**
     * Update password for selected user data
     *
     * @param \App\Http\Requests\Customer\v1\ResetPasswordRequest $request
     * @param \App\Modules\Auth\Actions\ResetPasswordAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action): JsonResponse
    {
        $dto = ResetPasswordDTO::fromRequest($request);

        $action->execute($dto);

        return new JsonResponse();
    }
}

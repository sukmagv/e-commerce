<?php

namespace App\Http\Controllers\Api\Customer\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\Customer;
use App\Modules\Auth\Actions\LoginAction;
use App\Modules\Auth\Actions\RegisterAction;
use App\Modules\Auth\Actions\ResetPasswordAction;
use App\Modules\Auth\Actions\ForgotPasswordAction;
use App\Http\Requests\Api\Customer\V1\LoginRequest;
use App\Http\Resources\Api\Auth\V1\CustomerResource;
use App\Http\Requests\Api\Customer\V1\RegisterRequest;
use App\Http\Requests\Api\Customer\V1\ResetPasswordRequest;
use App\Http\Requests\Api\Customer\V1\ForgotPasswordRequest;

class AuthController extends Controller
{
    /**
     * Create user and customer data
     *
     * @param \App\Http\Requests\Api\Customer\V1\RegisterRequest $request
     * @param \App\Modules\Auth\Actions\RegisterAction $action
     * @return \App\Http\Resources\Api\Auth\V1\CustomerResource
     */
    public function register(RegisterRequest $request, RegisterAction $action): CustomerResource
    {
        $customerData = $action->execute($request->payload());

        return new CustomerResource($customerData);
    }

    /**
     * Customer login and generate access token
     *
     * @param \App\Http\Requests\Api\Customer\V1\LoginRequest $request
     * @param \App\Modules\Auth\Actions\LoginAction $action
     * @return \App\Http\Resources\Api\Auth\V1\CustomerResource
     */
    public function login(LoginRequest $request, LoginAction $action): CustomerResource
    {
        $user = $action->execute($request->payload());

        $customer = Customer::query()
            ->where('user_id', $user->id)
            ->first();

        $customer->token = $user->token;

        return new CustomerResource($customer);
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
     * @param \App\Http\Requests\Api\Customer\V1\ForgotPasswordRequest $request
     * @param \App\Modules\Auth\Actions\ForgotPasswordAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action): JsonResponse
    {
        $action->execute($request->payload());

        return new JsonResponse();
    }

    /**
     * Update password for selected user data
     *
     * @param \App\Http\Requests\Api\Customer\V1\ResetPasswordRequest $request
     * @param \App\Modules\Auth\Actions\ResetPasswordAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action): JsonResponse
    {
        $action->execute($request->payload());

        return new JsonResponse();
    }
}

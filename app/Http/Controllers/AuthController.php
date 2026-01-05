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
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Create user and customer data
     *
     * @param RegisterRequest $request
     * @param RegisterAction $action
     * @return JsonResponse
     */
    public function register(RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $dto = CustomerRegisterDTO::fromRequest($request);

        $customer = $action->execute($dto);

        return response()->json($customer, Response::HTTP_OK);
    }

    /**
     * Customer login and generate access token
     *
     * @param LoginRequest $request
     * @param LoginAction $action
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $dto = CustomerLoginDTO::fromRequest($request);

        $user = $action->execute($dto);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ], Response::HTTP_OK);
    }

    /**
     * Customer logout
     *
     * @param Request $request
     * @return void
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(Response::HTTP_OK);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
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
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtpRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Auth\Actions\SendOtpAction;
use App\Modules\Auth\Actions\VerifyOtpAction;
use App\Modules\Auth\DTOs\SendOtpDTO;
use Symfony\Component\HttpFoundation\Response;

class OtpController extends Controller
{
    /**
     * Generate and Send OTP to customer
     *
     * @param \App\Http\Requests\OtpRequest $request
     * @param \App\Modules\Auth\Actions\SendOtpAction $action
     * @return JsonResponse
     */
    public function sendOtp(OtpRequest $request, SendOtpAction $action): JsonResponse
    {
        $dto = SendOtpDTO::fromRequest($request);

        $action->execute($dto);

        return new JsonResponse();
    }

    /**
     * Verify OTP submitted by customer
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Modules\Auth\Actions\VerifyOtpAction $action
     * @return JsonResponse
     */
    public function verifyOtp(Request $request, VerifyOtpAction $action): JsonResponse
    {
        $action->execute($request->otp_id, $request->code);

        return new JsonResponse();
    }
}

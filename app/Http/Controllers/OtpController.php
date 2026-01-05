<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtpRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Modules\Auth\Actions\SendOtpAction;
use App\Modules\Auth\Actions\VerifyOtpAction;
use Symfony\Component\HttpFoundation\Response;

class OtpController extends Controller
{
    /**
     * Generate and Send OTP to customer
     *
     * @param OtpRequest $request
     * @param SendOtpAction $action
     * @return JsonResponse
     */
    public function sendOtp(OtpRequest $request, SendOtpAction $action): JsonResponse
    {
        $otp = $action->execute($request->address);

        return response()->json(Response::HTTP_OK);
    }

    /**
     * Verify OTP submitted by customer
     *
     * @param Request $request
     * @param VerifyOtpAction $action
     * @return JsonResponse
     */
    public function verifyOtp(Request $request, VerifyOtpAction $action): JsonResponse
    {
        $action->execute($request->otp_id, $request->code);

        return response()->json(Response::HTTP_OK);
    }
}

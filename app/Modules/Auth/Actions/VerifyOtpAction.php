<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\Otp;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerifyOtpAction
{
    protected int $maxAttempts = 3;

    /**
     * Verify submitted OTP
     *
     * @param integer $id
     * @param string $code
     * @return boolean
     */
    public function execute(int $id, string $code): bool
    {
        $otp = Otp::query()
            ->where('id', $id)
            ->whereNull('verified_at')
            ->where('expired_at', '>', now())
            ->first();

        if (!$otp) {
            throw new HttpResponseException(response()->json([
                'message' => 'OTP expired',
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        if (!hash_equals($otp->code, hash('sha256', $code))) {
            $otp->attempt = ($otp->attempt ?? 0) + 1;

            if ($otp->attempt >= 3) {
                $otp->expired_at = now();
            }

            $otp->save();

            throw new HttpResponseException(response()->json([
                'message' => 'OTP invalid',
                'attempt' => $otp->attempt
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $otp->update([
            'verified_at' => now(),
            'attempt' => ($otp->attempt ?? 0) + 1
        ]);

        return true;
    }

}

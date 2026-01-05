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
        $record = Otp::query()
            ->where('id', $id)
            ->whereNull('verified_at')
            ->where('expired_at', '>', now())
            ->first();

        if (!$record) {
            throw new HttpResponseException(response()->json([
                'message' => 'OTP expired',
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        if (!hash_equals($record->code, hash('sha256', $code))) {
            $record->attempt = ($record->attempt ?? 0) + 1;

            if ($record->attempt >= 3) {
                $record->expired_at = now();
            }

            $record->save();

            throw new HttpResponseException(response()->json([
                'message' => 'OTP invalid',
                'attempt' => $record->attempt
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }

        $record->update([
            'verified_at' => now(),
            'attempt' => ($record->attempt ?? 0) + 1
        ]);

        return true;
    }

}
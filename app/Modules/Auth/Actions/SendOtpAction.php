<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\Otp;
use App\Modules\Auth\Enums\OtpType;
use Illuminate\Support\Facades\Mail;
use App\Modules\Auth\Mail\SendOtpMail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class SendOtpAction
{
    /**
     * Check request OTP attemps and generate OTP
     * Send OTP via email
     *
     * @param string $address
     * @param integer|null $otpId
     * @return Otp
     */
    public function execute(string $address, ?int $otpId = null): Otp
    {
        $otp = (string) random_int(100000, 999999);
        $hashedOtp = hash('sha256', $otp);
        $expiredAt = now()->addMinutes(10);
        $blockTime = 30;

        if ($otpId) {
            $record = Otp::find($otpId);
        } else {
            $record = Otp::query()
                ->where('address', $address)
                ->latest('id')
                ->first();
        }

        if ($record) {
            if ($record->attempt >= 3 && now()->lt($record->expired_at->copy()->addMinutes($blockTime))) {
                throw new HttpResponseException(response()->json([
                    'message' => 'Maximum attempts reached. Try again in 30 minutes.'
                ], Response::HTTP_TOO_MANY_REQUESTS));
            }

            $record->update([
                'code'       => $hashedOtp,
                'expired_at' => $expiredAt,
                'attempt'    => 0,
            ]);
        } else {
            $record = Otp::create([
                'address'    => $address,
                'type'       => OtpType::EMAIL,
                'code'       => $hashedOtp,
                'expired_at' => $expiredAt,
                'attempt'    => 0,
            ]);
        }

        Mail::to($address)->send(new SendOtpMail($otp));

        return $record;
    }
}

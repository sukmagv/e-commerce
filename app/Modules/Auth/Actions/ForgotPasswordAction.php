<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\ForgotPasswordDTO;
use App\Modules\Auth\Models\Otp;
use Illuminate\Support\Facades\Mail;
use App\Modules\Auth\Mail\SendOtpMail;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForgotPasswordAction
{
    /**
     * Generate and send OTP
     *
     * @param \App\Modules\Auth\DTOs\ForgotPasswordDTO $dto
     * @return boolean
     */
    public function execute(ForgotPasswordDTO $dto): bool
    {
        $code      = (string) random_int(100000, 999999);
        $hashedCode = hash('sha256', $code);
        $expiredAt = now()->addMinutes(10);
        $blockTime = 30;

        $otp = Otp::find($dto->otp_id);

        if ($otp && $otp->attempt >= 3 &&
            now()->lt($otp->expired_at->copy()->addMinutes($blockTime))
        ) {
            throw new HttpResponseException(
                response()->json(
                    ['message' => 'Maximum attempts reached. Try again in 30 minutes.'],
                    Response::HTTP_TOO_MANY_REQUESTS
                )
            );
        }

        if ($otp) {
            $otp->update([
                'code'       => $hashedCode,
                'expired_at' => $expiredAt,
                'attempt'    => 0,
            ]);
        } else {
            $otp = Otp::create([
                'address'    => $dto->address,
                'code'       => $hashedCode,
                'expired_at' => $expiredAt,
                'attempt'    => 0,
            ]);
        }

        Mail::to($dto->address)->send(new SendOtpMail($code));

        return true;
    }
}

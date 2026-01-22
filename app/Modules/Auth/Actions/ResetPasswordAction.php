<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\Otp;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use App\Modules\Auth\DTOs\ResetPasswordDTO;

class ResetPasswordAction
{
    /**
     * Update password
     *
     * @param \App\Modules\Auth\DTOs\ResetPasswordDTO $dto
     * @return boolean
     */
    public function execute(ResetPasswordDTO $dto): bool
    {
        DB::beginTransaction();
        try {
            $otp = Otp::find($dto->otpId);

            User::firstWhere('email', $otp->address)?->update([
                'password' => $dto->password,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        // $otp->delete();

        return true;
    }
}

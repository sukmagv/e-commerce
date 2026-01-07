<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\Otp;
use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Modules\Auth\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Modules\Auth\DTOs\ResetPasswordDTO;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class ResetPasswordAction
{
    public function execute(ResetPasswordDTO $dto): bool
    {
        DB::beginTransaction();
        try {
            $otp = Otp::find($dto->otp_id);

            User::firstWhere('email', $otp->address)?->update([
                'password' => $dto->password,
            ]);

            DB::commit();

            return true;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        // $otp->delete();
    }
}

<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\DTOs\ChangePasswordDTO;
use Illuminate\Validation\ValidationException;

class ChangePasswordAction
{
    /**
     * Change password in customer profile
     *
     * @param \App\Modules\Auth\DTOs\ChangePasswordDTO $dto
     * @return boolean
     */
    public function execute(User $user, ChangePasswordDTO $dto): bool
    {
        DB::beginTransaction();
        try {
            if (!Hash::check($dto->currentPassword, $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Current password is incorrect.'],
                ]);
            }

            $user->update([
                'password' => $dto->newPassword,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return true;
    }
}

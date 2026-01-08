<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\UpdateProfileRequest;

class UpdateProfileAction
{
    /**
     * Update one or more customer profile
     *
     * @param \App\Modules\Auth\Models\User $user
     * @param \App\Http\Requests\UpdateProfileRequest $request
     * @return \App\Modules\Auth\Models\User
     */
    public function execute(User $user, UpdateProfileRequest $request): User
    {
        DB::beginTransaction();
        try {
            $user->update($request->only('name', 'email'));

            $customerData = $request->only('phone');

            if ($request->hasFile('photo') && $request->file('photo') instanceof UploadedFile) {
                $customerData['photo'] = $user->customer->savePhoto($request->file('photo'));
            }

            if ($user->customer && $customerData) {
                $user->customer->update($customerData);
            }

            $user->load('customer', 'role');

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $user;
    }
}

<?php

namespace App\Modules\Auth\Actions;

use Illuminate\Http\Request;
use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateProfileAction
{
    /**
     * Update one or more customer profile
     *
     * @param \App\Modules\Auth\Models\User $user
     * @param \Illuminate\Http\Request $request
     * @return \App\Modules\Auth\Models\User
     */
    public function execute(User $user, Request $request): User
    {
        DB::beginTransaction();
        try {
            $user->update($request->only('name', 'email'));

            $customerData = $request->only('phone');

            if ($request->hasFile('photo') && $request->file('photo') instanceof UploadedFile) {
                $path = $request->file('photo')->store('uploads', 'public');
                $customerData['photo'] = $path;

                if ($user->customer && $user->customer->photo) {
                    Storage::disk('public')->delete($user->customer->photo);
                }
            }

            if ($user->customer && $customerData) {
                $user->customer->update($customerData);
            }

            DB::commit();

            $user = User::with(['customer', 'role'])->find($user->id);

            return $user;

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

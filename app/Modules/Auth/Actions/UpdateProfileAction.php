<?php

namespace App\Modules\Auth\Actions;

use App\Services\FileService;
use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Customer\v1\UpdateProfileRequest;

class UpdateProfileAction
{
    public function __construct(protected FileService $fileService)
    {}

    /**
     * Update one or more customer profile
     *
     * @param \App\Modules\Auth\Models\User $user
     * @param \App\Http\Requests\Customer\v1\UpdateProfileRequest $request
     * @return \App\Modules\Auth\Models\User
     */
    public function execute(User $user, UpdateProfileRequest $request): User
    {
        DB::beginTransaction();
        try {
            $user->update($request->only('name'));

            $customerData = $request->only('phone');

            $oldPath = $user->customer->photo;

            if ($request->hasFile('photo') && $request->file('photo') instanceof UploadedFile) {
                $customerData['photo'] = $this->fileService->updateOrCreate($request->photo, $oldPath, 'profile');
            }

            if ($user->customer && $customerData) {
                $user->customer->update($customerData);
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $user;
    }
}

<?php

namespace App\Modules\Auth\Actions;

use App\Services\FileService;
use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\DTOs\UpdateProfileDTO;

class UpdateProfileAction
{
    public function __construct(protected FileService $fileService)
    {}

    /**
     * Update one or more customer profile
     *
     * @param \App\Modules\Auth\Models\User $user
     * @param \App\Http\Requests\Api\Customer\V1\UpdateProfileRequest $request
     * @return \App\Modules\Auth\Models\User
     */
    public function execute(UpdateProfileDTO $dto): User
    {
        DB::beginTransaction();
        try {
            /** @var \App\Modules\Auth\Models\User $user */
            $user = Auth::user();

            $user->update(['name' => $dto->name]);

            $customerData = $dto->phone;

            $oldPath = $user->customer->photo;

            if ($dto->photo && $dto->photo instanceof UploadedFile) {
                $customerData['photo'] = $this->fileService->updateOrCreate($dto->photo, $oldPath, 'profile');
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

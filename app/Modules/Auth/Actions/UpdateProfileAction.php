<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\UpdateProfileDTO;
use App\Modules\Auth\Models\Customer;
use App\Modules\Auth\Models\User;
use App\Services\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UpdateProfileAction
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Update one or more customer profile
     *
     * @param  \App\Modules\Auth\Models\User  $user
     * @param  \App\Http\Requests\Api\Customer\V1\UpdateProfileRequest  $request
     */
    public function execute(UpdateProfileDTO $dto): User
    {
        DB::beginTransaction();
        try {
            /** @var \App\Modules\Auth\Models\User $user */
            $user = Auth::user();

            if ($dto->name !== null) {
                $user->update(['name' => $dto->name]);
            }

            if ($dto->phone !== null) {
                $customerData['phone'] = $dto->phone;
            }

            $oldPath = $user->customer?->photo;

            if ($dto->photo && $dto->photo instanceof UploadedFile) {
                $customerData['photo'] = $this->fileService->updateOrCreate($dto->photo, $oldPath, Customer::IMAGE_PATH);
            }

            if ($user->customer && ! empty($customerData)) {
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

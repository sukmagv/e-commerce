<?php

namespace App\Http\Controllers\Customer\v1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProfileResource;
use App\Modules\Auth\DTOs\ChangePasswordDTO;
use App\Http\Requests\Customer\v1\UpdateProfileRequest;
use App\Http\Requests\Customer\v1\ChangePasswordRequest;
use App\Modules\Auth\Actions\UpdateProfileAction;
use App\Modules\Auth\Actions\ChangePasswordAction;

class ProfileController extends Controller
{
    /**
     * Retrieve customer profile
     *
     * @return \App\Http\Resources\v1\ProfileResource
     */
    public function getProfile(): ProfileResource
    {
        /** @var \App\Modules\Auth\Models\User $user */
        $user = Auth::user();

        $user->load(['customer', 'role']);

        return new ProfileResource($user);
    }

    /**
     * Update customer profile
     *
     * @param \App\Http\Requests\Customer\v1\UpdateProfileRequest $request
     * @param \App\Modules\Auth\Actions\UpdateProfileAction $action
     * @return \App\Http\Resources\ProfileResource
     */
    public function updateProfile(UpdateProfileRequest $request, UpdateProfileAction $action): ProfileResource
    {
        $updatedUser = $action->execute($request->user(), $request);

        return new ProfileResource($updatedUser);
    }

    /**
     * Change password from profile
     *
     * @param \App\Http\Requests\Customer\v1\ChangePasswordRequest $request
     * @param \App\Modules\Auth\Actions\ChangePasswordAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request, ChangePasswordAction $action): JsonResponse
    {
        $user = $request->user();

        $dto = ChangePasswordDTO::fromRequest($request);

        $action->execute($user, $dto);

        return new JsonResponse();
    }
}

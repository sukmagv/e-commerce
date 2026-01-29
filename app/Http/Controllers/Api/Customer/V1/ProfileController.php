<?php

namespace App\Http\Controllers\Api\Customer\V1;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\Actions\UpdateProfileAction;
use App\Modules\Auth\Actions\ChangePasswordAction;
use App\Http\Resources\Api\Auth\V1\ProfileResource;
use App\Http\Requests\Api\Customer\V1\UpdateProfileRequest;
use App\Http\Requests\Api\Customer\V1\ChangePasswordRequest;

class ProfileController extends Controller
{
    /**
     * Retrieve customer profile
     *
     * @return \App\Http\Resources\Api\Auth\V1\ProfileResource
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
     * @param \App\Http\Requests\Api\Customer\V1\UpdateProfileRequest $request
     * @param \App\Modules\Auth\Actions\UpdateProfileAction $action
     * @return \App\Http\Resources\Api\Auth\V1\ProfileResource
     */
    public function updateProfile(UpdateProfileRequest $request, UpdateProfileAction $action): ProfileResource
    {
        $updatedUser = $action->execute($request->payload());

        return new ProfileResource($updatedUser);
    }

    /**
     * Change password from profile
     *
     * @param \App\Http\Requests\Api\Customer\V1\ChangePasswordRequest $request
     * @param \App\Modules\Auth\Actions\ChangePasswordAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request, ChangePasswordAction $action): JsonResponse
    {
        $user = $request->user();

        $action->execute($user, $request->payload());

        return new JsonResponse();
    }
}

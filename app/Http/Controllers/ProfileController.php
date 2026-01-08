<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Modules\Auth\Actions\UpdateProfileAction;
use App\Modules\Auth\Actions\ChangePasswordAction;
use App\Modules\Auth\DTOs\ChangePasswordDTO;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Retrieve customer profile
     *
     * @return \App\Http\Resources\ProfileResource
     */
    public function getProfile(): ProfileResource
    {
        return new ProfileResource(Auth::user());
    }

    /**
     * Update customer profile
     *
     * @param \App\Http\Requests\UpdateProfileRequest $request
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
     * @param \App\Http\Requests\ChangePasswordRequest $request
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

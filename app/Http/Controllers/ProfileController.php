<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\Auth\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Modules\Auth\Actions\UpdateProfileAction;
use App\Modules\Auth\Actions\ChangePasswordAction;
use App\Modules\Auth\DTOs\ChangePasswordDTO;

class ProfileController extends Controller
{
    /**
     * Retrieve customer profile
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\ProfileResource
     */
    public function getProfile(Request $request): ProfileResource
    {
        $user = $request->user();

        $user = User::with(['customer', 'role'])->find($user->id);

        return new ProfileResource($user);
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
        $user = $request->user();

        $updatedUser = $action->execute($user, $request);

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

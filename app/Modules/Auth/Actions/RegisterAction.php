<?php

namespace App\Modules\Auth\Actions;

use App\Services\FileService;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use App\Modules\Auth\Models\Customer;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class RegisterAction
{
    public function __construct(protected FileService $fileService)
    {}

    /**
     * Check email already verified or not
     * Create new user and customer data
     *
     * @param \App\Modules\Auth\DTOs\CustomerRegisterDTO $dto
     * @return \App\Modules\Auth\Models\Customer
     */
    public function execute(CustomerRegisterDTO $dto): Customer
    {
        $path = null;

        DB::beginTransaction();
        try {
            $user = User::create(array_merge(
                $dto->toUserData(),
                ['role_id' => User::customerRole()],
            ));

            $customer = new Customer($dto->toCustomerData());

            if ($dto->photo) {
                $path = $this->fileService->updateOrCreate($dto->photo, null, 'profile');
                $customer->photo = $path;
            }

            $customer->user()->associate($user);
            $customer->save();

            $token = $user->generateToken();

            $customer->token = $token;

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($path) {
                $this->fileService->delete($path, 'profile');
            }

            throw $e;
        }

        // $otp->delete();

        return $customer;
    }
}

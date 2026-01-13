<?php

namespace App\Modules\Auth\Actions;

use App\Services\FileService;
use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use App\Modules\Auth\Models\Customer;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class RegisterAction
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

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
            $user = User::create([
                'role_id' => User::customerRole(),
                'name' => $dto->name,
                'email' => $dto->email,
                'password' => $dto->password,
            ]);

            $customer = new Customer($dto->toCustomerData());

            if ($dto->photo) {
                $path = $this->fileService->updateOrUpload($dto->photo, null, 'profile');
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
                $this->fileService->delete($path);
            }

            throw $e;
        }

        // $otp->delete();

        return $customer;
    }
}

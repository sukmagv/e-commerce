<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Support\Facades\DB;
use App\Modules\Auth\Models\Customer;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class RegisterAction
{
    /**
     * Check email already verified or not
     * Create new user and customer data
     *
     * @param \App\Modules\Auth\DTOs\CustomerRegisterDTO $dto
     * @return \App\Modules\Auth\Models\Customer
     */
    public function execute(CustomerRegisterDTO $dto): Customer
    {
        $photoPath = null;

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
                $photoPath = $customer->savePhoto($dto->photo);
                $customer->photo = $photoPath;
            }

            $customer->user()->associate($user);
            $customer->save();

            $token = $user->generateToken();

            $customer->token = $token;

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            $customer->deletePhoto($photoPath);

            throw $e;
        }

        // $otp->delete();

        return $customer;
    }
}

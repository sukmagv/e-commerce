<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use App\Modules\Auth\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;

class RegisterAction
{
    /**
     * Check email already verified or not
     * Create new user and customer data
     *
     * @param \App\Modules\Auth\DTOs\CustomerRegisterDTO $dto
     * @return array
     */
    public function execute(CustomerRegisterDTO $dto): array
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

            $customerData = $dto->toCustomerData();

            if ($dto->photo instanceof UploadedFile) {
                $photoPath = $dto->photo->store('uploads', 'public');
                $customerData['photo'] = $photoPath;
            }

            $customer = Customer::createWithUser($customerData, $user);

            $token = $user->generateToken();

            DB::commit();

            return [
                'user' => $user,
                'customer' => $customer,
                'token' => $token,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $e;
        }

        // $otp->delete();
    }
}

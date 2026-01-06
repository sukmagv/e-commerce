<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\Models\Otp;
use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\Models\Customer;
use Illuminate\Support\Facades\Storage;
use App\Modules\Auth\DTOs\CustomerRegisterDTO;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterAction
{
    /**
     * Check email already verified or not
     * Create new user and customer data
     *
     * @param \App\Modules\Auth\DTOs\CustomerRegisterDTO $dto
     * @return
     */
    public function execute(CustomerRegisterDTO $dto)
    {
        $photoPath = null;

        try {
            $customer = DB::transaction(function () use ($dto, &$photoPath) {
                $user = User::create([
                    'role_id' => User::customerRole(),
                    'name' => $dto->name,
                    'email' => $dto->email,
                    'password' => $dto->password,
                ]);

                $data = [
                    'user_id' => $user->id,
                ];

                if (!empty($dto->phone)) {
                    $data['phone'] = $dto->phone;
                }

                if ($dto->photo instanceof UploadedFile) {
                    $photoPath = $dto->photo->store('uploads', 'public');
                    $data['photo'] = $photoPath;
                }

                $customer = Customer::createWithUser($data, $user);

                return [
                    'user_id' => $user->id,
                    'code' => $customer->code,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $customer->phone,
                    'photo' => $customer->photo ? Storage::url($customer->photo) : null,
                ];
            });

            return $customer;

        } catch (\Throwable $e) {

            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            throw $e;
        }

        $otp->delete();

        return $data;
    }
}

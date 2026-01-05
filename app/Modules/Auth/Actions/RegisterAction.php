<?php

namespace App\Modules\Auth\Actions;

use App\Modules\Auth\DTOs\CustomerRegisterDTO;
use App\Modules\Auth\Models\Otp;
use App\Modules\Auth\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use App\Modules\Auth\Models\Customer;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterAction
{
    /**
     * Check email already verified or not
     * Create new user and customer data
     *
     * @param CustomerRegisterDTO $dto
     * @return array
     */
    public function execute(CustomerRegisterDTO $dto): array
    {
        $otp = Otp::query()
            ->where('id', $dto->otp_id)
            ->where('address', $dto->email)
            ->whereNotNull('verified_at')
            ->latest('verified_at')
            ->first();

        if (!$otp) {
            throw new HttpResponseException(
                response()->json(Response::HTTP_UNPROCESSABLE_ENTITY)
            );
        }

        $user = User::create([
            'role_id' => 2,
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => Hash::make($dto->password),
        ]);

        $photoPath = null;

        if ($dto->photo instanceof UploadedFile) {
            $photoPath = $dto->photo->store('uploads', 'public');
        }

        $code = $dto->code ?? $this->generateCustomerCode($dto->name);

        $customer = Customer::create([
            'user_id' => $user->id,
            'code' => $code,
            'phone' => $dto->phone,
            'photo' => $photoPath,
            'is_blocked' => $dto->is_blocked,
        ]);

        $customer->photo_url = $photoPath ? Storage::url($photoPath) : null;

        $otp->delete();

        return $customer;
    }

    /**
     * Generate customer code
     *
     * @param string $name
     * @return string
     */
    protected function generateCustomerCode(string $name): string
    {
        $initials = collect(explode(' ', strtoupper($name)))
            ->map(fn($word) => $word[0])
            ->join('');

        $lastId = Customer::max('id') ?? 0;

        return sprintf('CUS-%s-%03d', $initials ?: 'XX', $lastId + 1);
    }
}

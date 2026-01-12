<?php

namespace App\Rules;

use Closure;
use App\Modules\Auth\Models\Otp;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class VerifiedOtp implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        Otp::whereKey($value)
            ->whereNotNull('verified_at')
            ->exists() || $fail('OTP is not verified.', null);
    }
}

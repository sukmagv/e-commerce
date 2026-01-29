<?php

namespace App\Supports;

use BackedEnum;
use Illuminate\Validation\ValidationException;

trait EnsureStatus
{
    /**
     * Check data status
     *
     * @param BackedEnum $expectedStatus
     * @return void
     */
    public function ensureStatus(BackedEnum $expectedStatus): void
    {
        if ($this->status !== $expectedStatus) {
            throw ValidationException::withMessages([
                'message' => 'Invalid status.'
            ]);
        }
    }
}

<?php

namespace App\Supports;

use Illuminate\Validation\ValidationException;

trait EnsureStatus
{
    public function ensureStatus(string $expectedStatus): void
    {
        if ($this->status !== $expectedStatus) {
            throw ValidationException::withMessages([
                'message' => 'Invalid status. Expected status: ' . $expectedStatus
            ]);
        }
    }
}

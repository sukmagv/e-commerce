<?php

namespace App\Supports;

use Illuminate\Validation\ValidationException;

trait EnsureStatus
{
    /**
     * Check data status
     *
     * @param string $expectedStatus
     * @return void
     */
    public function ensureStatus(string $expectedStatus): void
    {
        if ($this->status->value !== $expectedStatus) {
            throw ValidationException::withMessages([
                'message' => 'Invalid status. Expected status: ' . $expectedStatus
            ]);
        }
    }
}

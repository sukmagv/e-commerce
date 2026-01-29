<?php

namespace App\Modules\Auth\DTOs;

use App\Supports\BaseDTO;
use Illuminate\Http\UploadedFile;

class UpdateProfileDTO extends BaseDTO
{
    public function __construct(
        public ?string $name,
        public ?string $phone,
        public ?UploadedFile $photo,
    ) {}
}

<?php

namespace App\Modules\Order\DTOs;

use App\Supports\BaseDTO;

class ProductSnapshotDTO extends BaseDTO
{
    public function __construct(
        public string $code,
        public int $category_id,
        public string $slug,
        public string $name,
        public string $photo,
        public float $price,
        public array $category,
    ) {}
}

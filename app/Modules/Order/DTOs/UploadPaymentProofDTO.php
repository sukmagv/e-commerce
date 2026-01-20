<?php

namespace App\Modules\Order\DTOs;

use App\Modules\Order\Enums\PaymentType;
use App\Modules\Product\Enums\DiscountType;
use App\Supports\BaseDTO;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class UploadPaymentProofDTO extends BaseDTO
{
    public UploadedFile $proof_link;
    public PaymentType $type;
    public ?string $note;
}

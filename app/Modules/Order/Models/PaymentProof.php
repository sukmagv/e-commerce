<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Modules\Order\Enums\PaymentType;
use App\Modules\Order\Enums\PaymentStatus;
use App\Supports\EnsureStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentProof extends Model
{
    use HasFactory, EnsureStatus;

    /** @var string */
    const FILE_PATH = 'paymentProof/';

    protected $fillable = [
        'payment_id',
        'type',
        'status',
        'proof_link',
        'note',
    ];

    protected $casts = [
        'type' => PaymentType::class,
        'status' => PaymentStatus::class,
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Accessor for the `proof_link` attribute.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function proofFile(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) =>
                $value ?? Storage::url(self::FILE_PATH . $value)
            );
    }
}

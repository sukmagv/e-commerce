<?php

namespace App\Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
    ];

    public function order(): HasOne
    {
        return $this->hasOne(Order::class);
    }

    public function proof(): HasMany
    {
        return $this->hasMany(PaymentProof::class);
    }

    /**
     * Get latest payment proof
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestProof(): HasOne
    {
        return $this->hasOne(PaymentProof::class)->latestOfMany();
    }
}

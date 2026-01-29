<?php

namespace App\Modules\Order\Models;

use App\Supports\HasCode;
use App\Supports\HasSearch;
use App\Supports\HasStatus;
use App\Supports\EnsureStatus;
use App\Supports\HasDateBetween;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Models\Customer;
use App\Modules\Order\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Order\Enums\OrderStatus;
use App\Supports\HasSorting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, HasCode, HasSearch, HasStatus, HasDateBetween, HasSorting, EnsureStatus;

    const TAX = 11 / 100;

    protected $fillable = [
        'user_id',
        'status',
        'code',
        'sub_total',
        'tax_amount',
        'grand_total',
        'note',
        'transaction_date',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'sub_total' => 'float',
        'tax_amount' => 'float',
        'grand_total' => 'float',
        'transaction_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): HasOne
    {
        return $this->hasOne(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public static function getTaxAmount(float $finalPrice): float
    {
        return ($finalPrice * self::TAX);
    }
}

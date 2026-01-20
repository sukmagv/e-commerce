<?php

namespace App\Modules\Order\Models;

use App\Supports\HasCode;
use App\Supports\HasSearch;
use App\Modules\Auth\Models\User;
use App\Modules\Auth\Models\Customer;
use App\Modules\Order\Models\OrderItem;
use Illuminate\Database\Eloquent\Model;
use App\Modules\Order\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, HasCode, HasSearch;

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
        'sub_total' => 'integer',
        'tax_amount' => 'integer',
        'grand_total' => 'integer',
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

    /**
     * Get order data based on selected status
     *
     * @param Illuminate\Database\Eloquent\Builder $query
     * @param string|null $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $query->when($status, fn (Builder $q) => $q->where('status', $status));
    }

    /**
     * Get order data between selected date
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateBetween(Builder $query, ?string $startDate = null, ?string $endDate = null): Builder {
        return $query
            ->when($startDate,fn (Builder $q) => $q->whereDate('created_at', '>=', $startDate))
            ->when($endDate,fn (Builder $q) => $q->whereDate('created_at', '<=', $endDate));
    }
}

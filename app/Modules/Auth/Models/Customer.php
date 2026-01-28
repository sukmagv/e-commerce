<?php

namespace App\Modules\Auth\Models;

use App\Supports\HasCode;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Modules\Auth\Models\Traits\HasUser;
use App\Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUser, HasCode;

    /** @var string */
    const IMAGE_PATH = 'profiles/';

    protected $fillable = [
        'user_id',
        'code',
        'phone',
        'photo',
        'is_blocked',
    ];

    protected $casts = [
        'is_blocked' => 'boolean'
    ];

    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) =>
                $value ? asset(Storage::url(self::IMAGE_PATH . $value)) : null
        );
    }
}

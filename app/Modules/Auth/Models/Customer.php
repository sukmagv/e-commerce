<?php

namespace App\Modules\Auth\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use App\Modules\Auth\Models\Traits\HasCode;
use App\Modules\Auth\Models\Traits\HasUser;
use App\Modules\Auth\Models\Traits\HasPhoto;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUser, HasCode;

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

    protected function photo(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? Storage::url($value) : null,
        );
    }
}

<?php

namespace App\Modules\Auth\Models;

use App\Modules\Auth\Enums\OtpType;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Otp extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'attempt',
        'address',
        'verified_at',
        'expired_at',
        'deleted_at'
    ];

    protected $casts = [
        'type' => OtpType::class,
        'verified_at' => 'datetime',
        'expired_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

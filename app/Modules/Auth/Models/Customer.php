<?php

namespace App\Modules\Auth\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modules\Auth\Models\Traits\HasCode;
use App\Modules\Auth\Models\Traits\HasPhoto;
use App\Modules\Auth\Models\Traits\HasUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable, HasUser, HasCode, HasPhoto;

    protected $fillable = [
        'user_id',
        'code',
        'phone',
        'photo',
        'is_blocked',
    ];

    protected $casts = [
        'code' => 'string',
        'is_blocked' => 'boolean'
    ];
}

<?php

namespace App\Modules\Auth\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modules\Auth\Models\Traits\HasCode;
use App\Modules\Auth\Models\Traits\HasUser;
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

    public static function createWithUser(array $attributes, User $user)
    {
        $attributes['user_id'] = $user->id;
        $attributes['code'] = static::generateCode($user->name, (new static)->code_prefix);

        return static::create($attributes);
    }
}

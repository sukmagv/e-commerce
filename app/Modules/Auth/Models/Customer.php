<?php

namespace App\Modules\Auth\Models;

use App\Traits\HasUser;
use App\Models\BaseModel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends BaseModel
{
    use HasApiTokens, HasFactory, Notifiable, HasUser;

    protected $fillable = [
        'user_id',
        'code',
        'phone',
        'photo',
        'is_blocked',
    ];

    protected $code_prefix = 'CUS';

    protected $initial_from_relation = [
        'relation' => 'user',
        'field' => 'name',
    ];

    public static function createWithUser(array $attributes, User $user)
    {
        $attributes['code'] = static::generateCode($user->name, (new static)->code_prefix);

        return static::create($attributes);
    }
}

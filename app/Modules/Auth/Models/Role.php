<?php

namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Role extends Model
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'slug',
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}

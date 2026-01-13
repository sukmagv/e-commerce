<?php

namespace App\Modules\Auth\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Modules\Auth\Models\Traits\HasSlug;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasApiTokens, Notifiable, HasSlug;

    protected $fillable = [
        'slug',
        'name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}

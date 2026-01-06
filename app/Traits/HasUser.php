<?php

namespace App\Traits;

use App\Modules\Auth\Models\User;

trait HasUser
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

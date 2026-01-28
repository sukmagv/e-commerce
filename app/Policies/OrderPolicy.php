<?php

namespace App\Policies;

use App\Modules\Auth\Models\User;
use App\Modules\Order\Models\Order;
use Illuminate\Auth\Access\Response;

class OrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->slug === 'customer';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $user->role->slug === 'customer'
            && $order->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->slug === 'customer';
    }


    public function uploadProof(User $user, Order $order): bool
    {
        return $this->view($user, $order);
    }


    public function printPdf(User $user, Order $order): bool
    {
        return $this->view($user, $order);
    }
}

<?php

namespace App\Supports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\Order;

class OrderObserver
{
    /**
     * Handle order updated
     */
    public function updated(Order $order)
{
    DB::afterCommit(function () use ($order) {
        $changes = collect($order->getChanges())->mapWithKeys(fn($val, $key) => [
            $key => ['old' => $order->getOriginal($key), 'new' => $val]
        ]);

        Log::channel('order')->info('ORDER UPDATED', [
            'order_code' => $order->code,
            'user_id'    => $order->user_id,
            'changes'    => $changes,
            'by'         => Auth::id(),
            'at'         => now()->toDateTimeString(),
        ]);
    });
}
}

<?php

namespace App\Supports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\Order;

class TransactionObserver
{
    /**
     * Handle order created
     */
    public function created(Order $order)
    {
        DB::afterCommit(function () use ($order) {
            $order->load(['orderItem.product', 'user']);

            $item = $order->orderItem;

            $product = $item ? [
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name ?? null,
                'qty'          => $item->qty,
                'final_price'  => $item->final_price,
            ] : null;

            Log::channel('order')->info('ORDER CREATED', [
                'order_code' => $order->code,
                'user_id' => $order->user_id,
                'user_name' => $order->user->name,
                'total' => $order->grand_total,
                'product' => $product,
                'created_by' => Auth::id(),
                'created_at' => now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * Handle order updated
     */
    public function updated(Order $order)
    {
        DB::afterCommit(function () use ($order) {
            $order->load(['orderItem.product', 'user']);

            // Get changed data: old & new
            $changes = [];
            foreach ($order->getChanges() as $key => $newValue) {
                $changes[$key] = [
                    'old' => $order->getOriginal($key),
                    'new' => $newValue,
                ];
            }

            Log::channel('order')->info('ORDER UPDATED', [
                'order_code' => $order->code,
                'user_id' => $order->user_id,
                'product' => $order->orderItem->product_id,
                'changed_attributes' => $changes,
                'updated_by' => Auth::id(),
                'updated_at' => now()->toDateTimeString(),
            ]);
        });
    }
}

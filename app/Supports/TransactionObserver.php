<?php

namespace App\Supports;

use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Modules\Order\Models\Payment;

class TransactionObserver
{
    // Revisi: log tracking untuk create dan update status order
    public function created(Order $order)
    {
        DB::afterCommit(function () use ($order) {
            $order->load(['orderItem.product', 'user']);

            $item = $order->orderItem;

            $product = $item ? [
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name ?? null,
                'qty'          => $item->qty,
                'price'        => $item->price,
                'subtotal'     => $item->subtotal,
            ] : null;

            Log::channel('order')->info('ORDER CREATED', [
                'order_id' => $order->id,
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
}

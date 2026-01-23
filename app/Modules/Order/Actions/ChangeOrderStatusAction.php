<?php

namespace App\Modules\Order\Actions;

use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;

class ChangeOrderStatusAction
{
    public function execute(Order $order, PaymentStatus $paymentStatus): Order
    {
        $order->ensureStatus(OrderStatus::IN_PROGRESS->value);
        
        DB::beginTransaction();
        try {
            $order->payment->latestProof->update([
                'status' => $paymentStatus,
            ]);

            $order->update([
                'status' => $paymentStatus->getRelatedOrderStatus(),
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $order;
    }
}

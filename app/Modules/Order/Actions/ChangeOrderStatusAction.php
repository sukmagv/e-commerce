<?php

namespace App\Modules\Order\Actions;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Enums\PaymentStatus;
use App\Modules\Order\Models\PaymentProof;

class ChangeOrderStatusAction
{
    public function __construct(protected FileService $fileService)
    {}

    /**
     * Change payment proof and order status
     *
     * @param \App\Modules\Order\Models\Order $order
     * @param array $paymentData
     * @return \App\Modules\Order\Models\Order
     */
    public function execute(Order $order, array $paymentData): Order
    {
        $order->ensureStatus(OrderStatus::IN_PROGRESS);

        $proof = $order->payment->proof;

        DB::beginTransaction();
        try {
            $proof->update([
                'status' => $paymentData['status'],
                'reason' => $paymentData['reason'] ?? null,
            ]);

            $order->update([
                'status' => $paymentData['status']->getRelatedOrderStatus(),
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $order;
    }
}

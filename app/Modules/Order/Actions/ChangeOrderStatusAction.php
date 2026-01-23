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

    public function execute(Order $order, PaymentStatus $paymentStatus, ?string $reason = null): Order
    {
        $order->ensureStatus(OrderStatus::IN_PROGRESS->value);

        $latestProof = $order->payment->latestProof;

        $isDeclined = $paymentStatus === PaymentStatus::DECLINED;

        DB::beginTransaction();
        try {
            $proofUpdate = ['status' => $paymentStatus];

            if ($reason && $isDeclined) {
                $proofUpdate['reason'] = $reason;
                $latestProof->delete();
            }

            $latestProof->update($proofUpdate);

            $order->update([
                'status' => $paymentStatus->getRelatedOrderStatus(),
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        if ($isDeclined) {
            $fileName = $latestProof->getRawOriginal('proof_link');
            $this->fileService->delete($fileName, PaymentProof::FILE_PATH);
        }

        return $order;
    }
}

<?php

namespace App\Modules\Order\Actions;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use App\Modules\Order\Models\Payment;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Enums\PaymentStatus;
use App\Modules\Order\Models\PaymentProof;
use App\Modules\Order\DTOs\UploadPaymentProofDTO;

class UploadPaymentProofAction
{
    public function __construct(protected FileService $fileService)
    {}

    public function execute(UploadPaymentProofDTO $dto, Order $order): PaymentProof
    {
        DB::beginTransaction();
        try {
            $payment = Payment::where('order_id', $order->id)->first();

            if (!$payment) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount'   => $order->grand_total,
                ]);
            }

            $paymentProof = new PaymentProof(array_merge(
                $dto->toArray(),
                ['status' => PaymentStatus::PENDING],
                ));

            if ($dto->proof_link) {
                $path = $this->fileService->updateOrCreate($dto->proof_link, null, 'paymentProof');
                $paymentProof->proof_link = $path;
            }

            $paymentProof->payment()->associate($payment);

            $paymentProof->save();

            $order->update([
                'status' => OrderStatus::IN_PROGRESS,
            ]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $paymentProof;
    }
}

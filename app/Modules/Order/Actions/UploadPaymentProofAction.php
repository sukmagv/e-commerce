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

    /**
     * Store payment proof from customer order
     *
     * @param \App\Modules\Order\DTOs\UploadPaymentProofDTO $dto
     * @param \App\Modules\Order\Models\Order $order
     * @return \App\Modules\Order\Models\PaymentProof
     */
    public function execute(UploadPaymentProofDTO $dto, Order $order): PaymentProof
    {
        $payment = Payment::where('order_id', $order->id)->first();

        $oldProof = $payment?->proof?->proof_link;

        DB::beginTransaction();
        try {
            if (!$payment) {
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'amount'   => $order->grand_total,
                ]);
            }

            $proofData = array_merge(
                $dto->toArray(),
                ['status' => PaymentStatus::PENDING],
            );

            if ($dto->proofLink) {
                $proofData['proof_link'] = $this->fileService->updateOrCreate($dto->proofLink, $oldProof, PaymentProof::FILE_PATH);
            }

            $paymentProof = PaymentProof::updateOrCreate(
                ['payment_id' => $payment->id],
                $proofData
            );

            $order->update(['status' => OrderStatus::IN_PROGRESS]);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            throw $e;
        }

        return $paymentProof;
    }
}

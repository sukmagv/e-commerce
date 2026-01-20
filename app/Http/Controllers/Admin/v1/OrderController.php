<?php

namespace App\Http\Controllers\Admin\v1;

use Illuminate\Http\Request;
use App\Supports\ExcelReport;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Enums\PaymentStatus;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $request->validate([
            'search'     => ['sometimes', 'string'],
            'status'     => ['sometimes', 'string', Rule::enum(OrderStatus::class)],
        ]);

        $orders = Order::with(['user', 'payment.latestProof'])
            ->search($request->query('search'))
            ->status($request->query('status'))
            ->latest()
            ->paginate($request->query('limit', 10));

        return OrderResource::collection($orders);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Order\Models\Order $order
     * @return \App\Http\Resources\OrderResource
     */
    public function show(Order $order): OrderResource
    {
        $order->loadMissing([
            'user:id,name',
            'payment.latestProof',
            'orderItem.product',
            'orderItem.discount',
        ]);

        return new OrderResource($order);
    }

    /**
     * Retrieve payment proof detail
     *
     * @param \App\Modules\Order\Models\Order $order
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function getProofDetail(Order $order): JsonResource
    {
        $order->loadMissing(['payment.proof']);

        $proofData = $order->payment->latestProof;

        return new JsonResource([
            'type'       => $proofData->type,
            'proof_link' => $proofData->proof_link,
            'note'       => $proofData->note,
        ]);
    }

    /**
     * Accept payment proof
     *
     * @param \App\Modules\Order\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function acceptProof(Order $order): JsonResponse
    {
        abort_if(
            $order->payment?->latestProof->status !== PaymentStatus::PENDING,
            Response::HTTP_FORBIDDEN,
            'Proof already accepted or declined'
        );

        DB::transaction(function () use ($order) {
            $order->payment->latestProof->update([
                'status' => PaymentStatus::ACCEPTED,
            ]);

            $order->update([
                'status' => OrderStatus::FINISHED,
            ]);
        });

        return new JsonResponse();
    }

    /**
     * Decline payment proof
     *
     * @param \App\Modules\Order\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function declineProof(Order $order): JsonResponse
    {
        abort_if(
            $order->payment?->latestProof->status !== PaymentStatus::PENDING,
            Response::HTTP_FORBIDDEN,
            'Proof already accepted or declined'
        );

        DB::transaction(function () use ($order) {
            $order->payment->latestProof->update([
                'status' => PaymentStatus::DECLINED,
            ]);

            $order->update([
                'status' => OrderStatus::DECLINED,
            ]);
        });

        return new JsonResponse();
    }

    /**
     * Export order data to excel
     *
     * @return void
     */
    public function excelReport()
    {
        // filter status dan range date

        $orders = Order::with([
            'user',
            'payment.latestProof',
            'orderItem.product',
            'orderItem.discount',
        ])
        ->get()
        ->map(fn($order) => [
            'Order Code'    => $order->code,
            'Order Status'  => $order->status->value,
            'Customer Name' => $order->user->name,
            'Items'         => $order->orderItems->map(fn($i)=>$i->product->name)->implode(', '),
            'Grand Total'   => $order->grand_total,
            'Payment Status'=> $order->payment?->latestProof->status->value,
            'Created At'    => $order->created_at,
        ]);

        $headings = ['Order Code', 'Customer Name', 'Items', 'Grand Total', 'Payment Status', 'Created At'];

        return Excel::download(new ExcelReport($orders, $headings), 'orders.xlsx');
    }
}

<?php

namespace App\Http\Controllers\Admin\v1;

use App\Supports\ExcelReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Modules\Order\Models\Order;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\OrderResource;
use App\Http\Requests\QueryParamRequest;
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
    public function index(QueryParamRequest $request)
    {
        $orders = Order::with(['user', 'payment.latestProof'])
            ->search($request->search)
            ->status($request->status)
            ->latest()
            ->paginate($request->limit ?? 20);

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
            'user',
            'payment.latestProof',
            'orderItems' => function ($q) {
                $q->with([
                    'product' => fn ($q) => $q->withTrashed(),
                    'discount' => fn ($q) => $q->withTrashed(),
                ]);
            },
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
                'status' => OrderStatus::IN_PROGRESS,
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
        $orders = Order::with([
            'user',
            'payment.latestProof',
            'orderItems' => function ($q) {
                $q->with([
                    'product' => fn ($q) => $q->withTrashed(),
                    'discount' => fn ($q) => $q->withTrashed(),
                ]);
            },
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

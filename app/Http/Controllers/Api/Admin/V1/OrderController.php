<?php

namespace App\Http\Controllers\Api\Admin\V1;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Exports\OrderExcelReport;
use Illuminate\Http\JsonResponse;
use App\Modules\Order\Models\Order;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Enums\PaymentStatus;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Api\Order\V1\OrderResource;
use App\Modules\Order\Actions\ChangeOrderStatusAction;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $request->validate([
            'search'     => ['sometimes', 'string'],
            'status'     => ['sometimes', 'string', Rule::enum(OrderStatus::class)],
            'limit'      => ['sometimes', 'numeric']
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
     * @return \App\Http\Resources\Api\Order\V1\OrderResource
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
    public function acceptProof(Order $order, ChangeOrderStatusAction $action): JsonResponse
    {
        $action->execute($order, [
            'status' => PaymentStatus::ACCEPTED
        ]);

        return new JsonResponse();
    }

    /**
     * Decline payment proof
     *
     * @param \App\Modules\Order\Models\Order $order
     * @return \Illuminate\Http\JsonResponse
     */
    public function declineProof(Request $request, Order $order, ChangeOrderStatusAction $action): JsonResponse
    {
        $request->validate([
            'reason' => ['nullable', 'string', 'max:100']
        ]);

        $action->execute($order, [
            'status' => PaymentStatus::DECLINED,
            'reason' => $request->input('reason'),
        ]);

        return new JsonResponse();
    }

    /**
     * Export order data to excel
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function excelReport(Request $request)
    {
        $request->validate([
            'status'     => ['sometimes', 'string', Rule::enum(OrderStatus::class)],
            'start_date' => ['sometimes', 'date'],
            'end_date'   => ['sometimes', 'date', 'after_or_equal:start_date'],
        ]);

        return Excel::download(
            new OrderExcelReport(
                $request->input('status'),
                $request->input('start_date'),
                $request->input('end_date')
            ),
            'orders.xlsx'
        );
    }
}

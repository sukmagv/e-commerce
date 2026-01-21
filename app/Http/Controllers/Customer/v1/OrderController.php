<?php

namespace App\Http\Controllers\Customer\v1;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Modules\Order\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Modules\Order\Enums\OrderStatus;
use App\Modules\Order\Models\BankAccount;
use App\Modules\Order\DTOs\CreateOrderDTO;
use App\Modules\Order\Actions\CreateOrderAction;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Order\DTOs\UploadPaymentProofDTO;
use App\Http\Requests\Customer\v1\CreateOrderRequest;
use App\Http\Requests\Customer\v1\PaymentProofRequest;
use App\Modules\Order\Actions\UploadPaymentProofAction;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

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
            'start_date' => ['sometimes', 'date'],
            'end_date'   => ['sometimes', 'date', 'after_or_equal:start_date'],
            'limit'      => ['sometimes', 'numeric']
        ]);

        /** @var \App\Modules\Auth\Models\User $user */
        $user = Auth::user();

        $orders = Order::query()
            ->with(['user:id,name', 'payment.latestProof'])
            ->where('user_id', $user->id)
            ->search($request->input('search'))
            ->status($request->input('status'))
            ->dateBetween($request->input('start_date'), $request->input('end_date'))
            ->latest()
            ->paginate($request->input('limit', 10));

        return OrderResource::collection($orders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Customer\v1\CreateOrderRequest $request
     * @param \App\Modules\Order\Actions\CreateOrderAction $action
     * @return \App\Http\Resources\OrderResource
     */
    public function store(CreateOrderRequest $request, CreateOrderAction $action): OrderResource
    {
        $dto = CreateOrderDTO::fromRequest($request);

        $order = $action->execute($dto);

        return new OrderResource($order);
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
     * Store uploaded payment proof
     *
     * @param \App\Http\Requests\Customer\v1\PaymentProofRequest $request
     * @param \App\Modules\Order\Models\Order $order
     * @param \App\Modules\Order\Actions\UploadPaymentProofAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProof(PaymentProofRequest $request, Order $order, UploadPaymentProofAction $action): JsonResponse
    {
        $this->authorize('uploadProof', $order);

        $dto = UploadPaymentProofDTO::fromRequest($request);

        $order = $action->execute($dto, $order);

        return new JsonResponse();
    }

    /**
     * Generate PDF
     *
     * @param \App\Modules\Order\Models\Order $order
     * @return void
     */
    public function printPdf(Order $order)
    {
        $this->authorize('printPdf', $order);

        $order->loadMissing([
            'user:id,name',
            'payment.latestProof',
            'orderItem.product',
            'orderItem.discount',
        ]);

        $pdf = Pdf::loadView('order.pdf', compact('order'));

        return $pdf->download("order-{$order->code}.pdf");
    }

    /**
     * Show bank list
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function getBanks(): JsonResource
    {
        $banks = BankAccount::all();

        return new JsonResource($banks);
    }
}

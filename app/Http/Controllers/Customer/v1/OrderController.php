<?php

namespace App\Http\Controllers\Customer\v1;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use App\Modules\Order\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Requests\QueryParamRequest;
use App\Modules\Order\Models\BankAccount;
use App\Modules\Order\DTOs\CreateOrderDTO;
use Illuminate\Validation\ValidationException;
use App\Modules\Order\Actions\CreateOrderAction;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Modules\Order\DTOs\UploadPaymentProofDTO;
use App\Http\Requests\Customer\v1\CreateOrderRequest;
use App\Http\Requests\Customer\v1\PaymentProofRequest;
use App\Modules\Order\Actions\UploadPaymentProofAction;
use App\Modules\Order\Enums\PaymentStatus;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(QueryParamRequest $request)
    {
        /** @var \App\Modules\Auth\Models\User $user */
        $user = Auth::user();

        $orders = Order::query()
            ->with(['user', 'payment.latestProof'])
            ->where('user_id', $user->id)
            ->search($request->search)
            ->status($request->status)
            ->dateBetween($request->start_date, $request->end_date)
            ->latest()
            ->paginate($request->limit ?? 20);

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
        if($order->user_id !== Auth::user()->id){
            throw ValidationException::withMessages([
                'message' => 'Unauthorized',
            ]);
        }

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
     * Store uploaded payment proof
     *
     * @param \App\Http\Requests\Customer\v1\PaymentProofRequest $request
     * @param \App\Modules\Order\Models\Order $order
     * @param \App\Modules\Order\Actions\UploadPaymentProofAction $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadProof(PaymentProofRequest $request, Order $order, UploadPaymentProofAction $action): JsonResponse
    {
        $dto = UploadPaymentProofDTO::fromRequest($request);

        $order = $action->execute($dto, $order);

        return new JsonResponse();
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

    public function getPdf(Order $order)
    {
        abort_if(
            $order->payment?->latestProof->status !== PaymentStatus::ACCEPTED,
            Response::HTTP_FORBIDDEN,
            'Payment not accepted'
        );

        $order->loadMissing([
            'user',
            'payment.latestProof',
            'orderItems' => fn ($q) => $q->with([
                'product' => fn ($q) => $q->withTrashed(),
                'discount' => fn ($q) => $q->withTrashed(),
            ]),
        ]);

        $pdf = Pdf::loadView('order.pdf', compact('order'));

        return $pdf->download("order-{$order->code}.pdf");
    }
}

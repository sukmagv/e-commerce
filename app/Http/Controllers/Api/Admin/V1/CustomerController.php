<?php

namespace App\Http\Controllers\Api\Admin\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\Customer;
use App\Http\Resources\Api\Auth\V1\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
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
            'sort_by'    => ['sometimes', 'string'],
            'sort_order' => ['sometimes', 'in:asc,desc'],
            'start_date' => ['sometimes', 'date'],
            'end_date'   => ['sometimes', 'date', 'after_or_equal:start_date'],
            'limit'      => ['sometimes', 'numeric']
        ]);

        $allowedFields = [
            'user_id'  => 'user_id',
            'code' => 'code',
            'name' => 'user.name'
        ];

        $customers = Customer::with('user')
            ->search($request->search)
            ->sortByRequest($request, $allowedFields)
            ->dateBetween($request->input('start_date'), $request->input('end_date'))
            ->paginate($request->limit ?? 20);

        return CustomerResource::collection($customers);
    }

    /**
     * Block customer account
     *
     * @param \App\Modules\Auth\Models\Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function block(Customer $customer): JsonResponse
    {
        DB::transaction(function () use ($customer) {
            $customer->update(['is_blocked' => true]);
        });

        return new JsonResponse();
    }

    /**
     * Unblock customer account
     *
     * @param \App\Modules\Auth\Models\Customer $customer
     * @return \Illuminate\Http\JsonResponse
     */
    public function unblock(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            $customer->update(['is_blocked' => false]);
        });

        return new JsonResponse();
    }
}

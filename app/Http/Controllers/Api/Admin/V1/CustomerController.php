<?php

namespace App\Http\Controllers\Api\Admin\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\Customer;
use App\Http\Resources\Api\Auth\V1\CustomerResource;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CustomerResource::collection(Customer::paginate(20));
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

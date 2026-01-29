<?php

namespace App\Http\Controllers\Api\Customer\V1;

use App\Http\Controllers\Controller;
use App\Modules\Order\Models\BankAccount;
use Illuminate\Http\Resources\Json\JsonResource;

class BankController extends Controller
{
    /**
     * Show bank list
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function index(): JsonResource
    {
        $banks = BankAccount::paginate(10);

        return new JsonResource($banks);
    }
}

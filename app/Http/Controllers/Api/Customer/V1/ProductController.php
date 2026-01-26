<?php

namespace App\Http\Controllers\Api\Customer\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Http\Resources\Api\Product\V1\ProductResource;

class ProductController extends Controller
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
            'limit'      => ['sometimes', 'numeric']
        ]);

        $products = Product::with('activeDiscount')
            ->search($request->search)
            ->active()
            ->latest()
            ->paginate($request->limit ?? 20);

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Product\Models\Product $product
     * @return \App\Http\Resources\Api\Product\V1\ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return (new ProductResource($product->loadMissing('activeDiscount')))->getDetail();
    }
}

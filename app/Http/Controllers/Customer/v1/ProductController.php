<?php

namespace App\Http\Controllers\Customer\v1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Modules\Product\Models\Product;

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
        $products = Product::with('activeDiscount')
            ->search($request->query('search'))
            ->latest()
            ->paginate($request->query('limit', 20));

        return ProductResource::collection($products);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Product\Models\Product $product
     * @return \App\Http\Resources\ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return (new ProductResource($product->loadMissing('activeDiscount')))->getDetail();
    }
}

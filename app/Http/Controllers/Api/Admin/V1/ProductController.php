<?php

namespace App\Http\Controllers\Api\Admin\V1;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Actions\CreateProductAction;
use App\Modules\Product\Actions\UpdateProductAction;
use App\Http\Resources\Api\Product\V1\ProductResource;
use App\Http\Requests\Api\Admin\V1\CreateProductRequest;
use App\Http\Requests\Api\Admin\V1\UpdateProductRequest;

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
            'sort_by'    => ['sometimes', 'string'],
            'sort_order' => ['sometimes', 'in:asc,desc'],
            'start_date' => ['sometimes', 'date'],
            'end_date'   => ['sometimes', 'date', 'after_or_equal:start_date'],
            'limit'      => ['sometimes', 'numeric']
        ]);

        $allowedFields = ['category_id', 'code', 'name', 'price'];

        $products = Product::with('activeDiscount')
            ->search($request->search)
            ->sortByRequest($request, $allowedFields)
            ->dateBetween($request->input('start_date'), $request->input('end_date'))
            ->paginate($request->limit ?? 20);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Api\Admin\V1\CreateProductRequest $request
     * @param \App\Modules\Product\Actions\CreateProductAction $action
     * @return \App\Http\Resources\Api\Product\V1\ProductResource
     */
    public function store(CreateProductRequest $request, CreateProductAction $action): ProductResource
    {
        $product = $action->execute($request->payload());

        return (new ProductResource($product->loadMissing('activeDiscount')))->getDetail();
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

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Api\Admin\V1\UpdateProductRequest $request
     * @param \App\Modules\Product\Models\Product $product
     * @param \App\Modules\Product\Actions\UpdateProductAction $action
     * @return  \App\Http\Resources\Api\Product\V1\ProductResource
     */
    public function update(UpdateProductRequest $request, Product $product, UpdateProductAction $action): ProductResource
    {
        $product = $action->execute($request->payload(), $product);

        return (new ProductResource($product->loadMissing('activeDiscount')))->getDetail();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Modules\Product\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return new JsonResponse();
    }
}

<?php

namespace App\Http\Controllers\Api\Admin\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Product\Models\ProductCategory;
use App\Http\Resources\Api\Product\V1\ProductCategoryResource;

class ProductCategoryController extends Controller
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
            'limit'      => ['sometimes', 'numeric'],
        ]);

        $allowedFields = ['code', 'name'];

        $categories = ProductCategory::query()
            ->search($request->query('search'))
            ->sortByRequest($request, $allowedFields)
            ->dateBetween($request->input('start_date'), $request->input('end_date'))
            ->paginate($request->query('limit', 10));

        return ProductCategoryResource::collection($categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \App\Http\Resources\Api\Product\V1\ProductCategoryResource
     */
    public function store(Request $request): ProductCategoryResource
    {
        $vallidatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $productCategory = ProductCategory::create($vallidatedData);

        return new ProductCategoryResource($productCategory);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Product\Models\ProductCategory
     * @return \App\Http\Resources\Api\Product\V1\ProductCategoryResource
     */
    public function show(ProductCategory $productCategory): ProductCategoryResource
    {
        return new ProductCategoryResource($productCategory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Modules\Product\Models\ProductCategory
     * @return \App\Http\Resources\Api\Product\V1\ProductCategoryResource
     */
    public function update(Request $request, ProductCategory $productCategory): ProductCategoryResource
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $productCategory->update($validatedData);

        return new ProductCategoryResource($productCategory);
    }
}

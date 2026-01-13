<?php

namespace App\Modules\Product\Actions;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Modules\Product\Models\Product;
use App\Modules\Product\DTOs\UpdateProductDTO;

class UpdateProductAction
{
    public function __construct(protected FileService $fileService)
    {}

    /**
     * Update product data
     *
     * @param
     * @param \App\Modules\Product\Models\Product $product
     * @return \App\Modules\Product\Models\Product Product
     */
    public function execute(UpdateProductDTO $dto, Product $product): Product
    {
        $path = null;

        DB::beginTransaction();
        try {
            $oldPath = $product->photo;

            $newProductData = [
                'name'        => $dto->name ?? $product->name,
                'category_id' => $dto->category_id ?? $product->category_id,
                'price'       => $dto->price ?? $product->price,
                'is_discount' => $dto->is_discount ?? $product->is_discount,
            ];

            if ($dto->photo) {
                $newProductData['photo'] = $this->fileService->updateOrCreate($dto->photo, $oldPath, 'products');
            }

            $product->update($newProductData);

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($path) {
                $this->fileService->delete($path);
            }

            throw $e;
        }

        return $product;
    }
}

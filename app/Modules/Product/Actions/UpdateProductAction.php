<?php

namespace App\Modules\Product\Actions;

use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductDiscount;
use App\Services\FileService;
use App\Supports\DiscountValidation;
use Illuminate\Support\Facades\DB;

class UpdateProductAction
{
    public function __construct(protected FileService $fileService) {}

    /**
     * Update product data
     * Soft delete product if product exists in order table and create new product
     *
     * @param \App\Modules\Product\DTOs\UpdateProductDTO $dto
     * @param \App\Modules\Product\Models\Product $product
     * @return \App\Modules\Product\Models\Product Product
     */
    public function execute(UpdateProductDTO $dto, Product $product): Product
    {
        $path = null;

        $oldPath = $product->photo;

        $updateData = $dto->toArray();

        $updateData = array_filter($updateData, fn ($value) => ! is_null($value));

        DB::beginTransaction();
        try {
            if (isset($dto->photo) && $dto->photo) {
                $updateData['photo'] = $this->fileService->updateOrCreate($dto->photo, $oldPath, Product::IMAGE_PATH);
            }

            $product->update($updateData);

            if (isset($dto->isDiscount) && $dto->isDiscount) {
                $discount = $this->applyDiscount($product, $dto);

                $discount->product()->associate($product);

                $discount->save();
            }

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

    /**
     * Replicate product and handle photo upload
     *
     * @param Product $product
     * @param UpdateProductDTO $dto
     * @param string|null $oldPath
     * @return Product
     */
    protected function replicateWithPhoto(Product $product, UpdateProductDTO $dto, ?string $oldPath): Product
    {
        $newProduct = $product->replicate([
            'code',
            'slug',
            'deleted_at',
            'created_at',
            'updated_at',
        ]);

        if (isset($dto->photo) && $dto->photo) {
            $newProduct->photo = $this->fileService->updateOrCreate($dto->photo, null, 'products');
        } else {
            $newProduct->photo = $oldPath;
        }
        $newProduct->fill(array_filter(
            $dto->toArray(),
            function ($value) {
                return ! is_null($value);
            }
        ));

        return $newProduct;
    }

    /**
     * Apply discount to product
     */
    protected function applyDiscount(Product $product, UpdateProductDTO $dto): ProductDiscount
    {
        DiscountValidation::calculateFinalPrice($product->price, $dto->discount);

        $discount = new ProductDiscount($dto->discount->toArray());

        return $discount;
    }
}

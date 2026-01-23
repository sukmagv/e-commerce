<?php

namespace App\Modules\Product\Actions;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Supports\DiscountValidation;
use App\Modules\Product\Models\Product;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Models\ProductDiscount;

class UpdateProductAction
{
    public function __construct(protected FileService $fileService)
    {}

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

        // Check product already used in order or not
        $isUsedInOrder = $product->orderItems()->exists();

        $oldPath = $product->photo;

        DB::beginTransaction();
        try {

            if ($isUsedInOrder) {
                $newProduct = $this->replicateWithPhoto($product, $dto, $oldPath);

                $newProduct->save();

                $product->delete();

            } else {
                $updateData = $dto->toArray();

                $updateData = array_filter($updateData, fn($value) => !is_null($value));

                if (isset($dto->photo) && $dto->photo) {
                    $updateData['photo'] = $this->fileService->updateOrCreate($dto->photo, $oldPath, 'products');
                }

                $product->update($updateData);
            }

            if (isset($dto->isDiscount) && $dto->isDiscount) {
                $discount = $this->applyDiscount($product, $dto);

                $discount->product()->associate($product);

                $discount->save();
            }

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($path) {
                $this->fileService->delete($path, 'product');
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
                function ($value) {return !is_null($value);}
            ));

        return $newProduct;
    }

    /**
     * Apply discount to product
     */
    protected function applyDiscount(Product $product, UpdateProductDTO $dto): ProductDiscount
    {
        DiscountValidation::calculateFinalPrice($product, $dto->discount);

        $discount = new ProductDiscount($dto->discount->toArray());

        return $discount;
    }
}

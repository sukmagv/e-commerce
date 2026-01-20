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

        DB::beginTransaction();
        try {
            // cek apakah product sudah dipakai di order
            $isUsedInOrder = $product->orderItems()->exists();

            $oldPath = $product->photo;

            if ($isUsedInOrder) {
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

                $newProduct->fill($dto->toArray());

                $newProduct->save();

                $product->delete();

            } else {
                $updateData = $dto->toArray();

                if (isset($dto->photo) && $dto->photo) {
                    $updateData['photo'] = $this->fileService->updateOrCreate($dto->photo, $oldPath, 'products');
                }

                $product->update($updateData);
            }

            if (isset($dto->is_discount) && $dto->is_discount) {
                DiscountValidation::calculateFinalPrice(
                    $dto->price,
                    $dto->discount->type,
                    $dto->discount->amount,
                    $dto->discount->final_price,
                );

                $discount = new ProductDiscount($dto->discount->toArray());

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
}

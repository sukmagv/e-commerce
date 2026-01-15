<?php

namespace App\Modules\Product\Actions;

use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use App\Modules\Product\Models\Product;
use App\Modules\Product\DTOs\UpdateProductDTO;
use App\Modules\Product\Models\ProductDiscount;

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
            // soft delete product ketika product sudah pernah diorder, update biasa ketika belum pernah masuk ke order

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

            if ($dto->type && $dto->amount !== null && $dto->final_price !== null) {
                $discount = new ProductDiscount([
                    'type'        => $dto->type,
                    'amount'      => $dto->amount,
                    'final_price' => $dto->final_price,
                ]);

                $discount->product()->associate($product);
                $discount->save();
            }

            // discount lama di soft delete jika sudah pernah diorder atau ada perubahan product price
            // update biasa jika belum ada di order

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

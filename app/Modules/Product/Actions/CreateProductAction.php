<?php

namespace App\Modules\Product\Actions;

use App\Services\FileService;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use App\Modules\Product\Models\Product;
use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\Models\ProductDiscount;
use App\Supports\DiscountValidation;

class CreateProductAction
{
    public function __construct(protected FileService $fileService)
    {}

    /**
     * Create new product data
     *
     * @param \App\Modules\Product\DTOs\CreateProductDTO $dto
     * @return \App\Modules\Product\Models\Product
     */
    public function execute(CreateProductDTO $dto): Product
    {
        $path = null;

        DB::beginTransaction();
        try {
            $product = new Product($dto->toArray());

            if ($dto->photo) {
                $path = $this->fileService->updateOrCreate($dto->photo, null, 'products');
                $product->photo = $path;
            }

            $product->save();

            if ($dto->is_discount) {
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

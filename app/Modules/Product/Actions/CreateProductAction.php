<?php

namespace App\Modules\Product\Actions;

use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductDiscount;
use App\Services\FileService;
use App\Supports\DiscountValidation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function __construct(protected FileService $fileService) {}

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

            if ($dto->photo instanceof UploadedFile) {
                $path = $this->fileService->updateOrCreate($dto->photo, null, Product::IMAGE_PATH);
                $product->photo = $path;
            }

            $product->save();

            if ($dto->isDiscount) {
                DiscountValidation::calculateFinalPrice($product->price, $dto->discount);

                $discount = new ProductDiscount($dto->discount->toArray());

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
}

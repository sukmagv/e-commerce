<?php

namespace App\Modules\Product\Actions;

use App\Services\FileService;
use InvalidArgumentException;
use Illuminate\Support\Facades\DB;
use App\Modules\Product\Models\Product;
use App\Modules\Product\DTOs\CreateProductDTO;
use App\Modules\Product\Enums\DiscountType;
use App\Modules\Product\Models\ProductDiscount;

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
            $this->validateDiscountCalculation($dto);

            if ($dto->photo) {
                $path = $this->fileService->updateOrCreate($dto->photo, null, 'products');
            }

            $product = Product::create([
                'category_id' => $dto->category_id,
                'name' => $dto->name,
                'photo' => $path,
                'price' => $dto->price,
                'is_discount' => $dto->is_discount,
            ]);

            if ($dto->is_discount) {
                $discount = new ProductDiscount([
                    'type'        => $dto->type,
                    'amount'      => $dto->amount,
                    'final_price' => $dto->final_price,
                ]);

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
     * Validate discount final price calculation
     *
     * @param \App\Modules\Product\DTOs\CreateProductDTO $dto
     * @return void
     */
    protected function validateDiscountCalculation(CreateProductDTO $dto): void
    {
        if (!$dto->is_discount) {
            return;
        }

        $expectedFinalPrice = match ($dto->type) {
            DiscountType::NOMINAL =>
                $dto->price - $dto->amount,

            DiscountType::PERCENTAGE =>
                $dto->price - ($dto->price * $dto->amount / 100),

            default => $dto->price,
        };

        if ($expectedFinalPrice < 0) {
            throw new InvalidArgumentException(
                "The discount amount exceeds the product price."
            );
        }

        if (round($expectedFinalPrice) !== round($dto->final_price)) {
            throw new InvalidArgumentException(
                "The final price calculation is not valid." . $expectedFinalPrice
            );
        }
    }
}

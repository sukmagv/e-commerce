<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('product_categories', 'id')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('photo');
            $table->unsignedBigInteger('price');
            $table->boolean('is_discount')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

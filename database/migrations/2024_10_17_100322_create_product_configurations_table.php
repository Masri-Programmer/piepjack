<?php

use App\Models\ProductItem;
use App\Models\VariationOption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_configurations', function (Blueprint $table) {
            $table->foreignIdFor(ProductItem::class, 'product_item_id');
            $table->foreignIdFor(VariationOption::class, 'variation_option_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_configurations');
    }
};

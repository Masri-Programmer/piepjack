<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('reviews_count')->default(0)->after('category_id');
            $table->decimal('reviews_avg_rating', 3, 2)->nullable()->after('reviews_count');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['reviews_count', 'reviews_avg_rating']);
        });
    }
};

<?php

use App\Models\Category;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->unique();
            $table->string('slug')->unique();
            $table->boolean('active')->default(false);
            $table->boolean('promoted')->default(false);
            $table->foreignIdFor(Category::class, 'parent_id')->nullable()->default(null)->constrained()->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

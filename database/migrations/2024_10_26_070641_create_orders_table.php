<?php

use App\Models\User;
use App\Models\Address;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->index();
            $table->foreignIdFor(User::class)->constrained();
            $table->foreignIdFor(Address::class, 'shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->foreignIdFor(Address::class, 'billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'paid', 'canceled', 'shipped', 'delivered'])->default('pending');
            $table->text('order_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

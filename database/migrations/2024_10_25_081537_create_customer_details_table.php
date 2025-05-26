<?php

use App\Models\Customer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class, 'customer_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('address_line_one');
            $table->string('address_line_two');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_details');
    }
};

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
        Schema::table('lunar_orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->index()->after('status');
            $table->string('label_url')->nullable()->after('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lunar_orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'label_url']);
        });
    }
};

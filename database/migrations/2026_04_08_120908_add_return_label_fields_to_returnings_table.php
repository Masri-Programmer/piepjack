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
        Schema::table('returnings', function (Blueprint $table) {
            $table->string('sendcloud_return_id')->nullable();
            $table->string('label_url')->nullable();
            $table->string('qr_code_url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('returnings', function (Blueprint $table) {
            $table->dropColumn(['sendcloud_return_id', 'label_url', 'qr_code_url']);
        });
    }
};

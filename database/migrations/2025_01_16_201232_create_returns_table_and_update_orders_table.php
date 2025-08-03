<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReturnsTableAndUpdateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('return_number')->unique()->index();
            $table->enum('status', ['not_requested', 'requested', 'approved', 'rejected'])->default('not_requested');
            $table->text('reason')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('return_id')->nullable()->constrained('returns')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['return_id']);
            $table->dropColumn('return_id');
        });

        Schema::dropIfExists('returns');
    }
}

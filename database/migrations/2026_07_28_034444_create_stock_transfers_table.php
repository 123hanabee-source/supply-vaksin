<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->integer('transfer_id')->primary();
            $table->integer('vaccine_id');
            $table->integer('from_facility_id');
            $table->integer('to_facility_id');
            $table->integer('quantity');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('completed');
            $table->integer('created_by')->nullable();
            $table->timestamp('transfer_date')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfers');
    }
};

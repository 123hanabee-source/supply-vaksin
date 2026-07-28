<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock', function (Blueprint $table) {
            $table->integer('stock_id')->primary();
            $table->integer('facility_id');
            $table->integer('vaccine_id');
            $table->integer('quantity')->nullable();

            $table->foreign('facility_id')->references('facility_id')->on('facilities');
            $table->foreign('vaccine_id')->references('vaccine_id')->on('vaccines');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution', function (Blueprint $table) {
            $table->integer('distribution_id')->primary();
            $table->integer('vaccine_id');
            $table->integer('facility_id');
            $table->integer('quantity');
            $table->date('distribution_date')->nullable();

            $table->foreign('vaccine_id')->references('vaccine_id')->on('vaccines');
            $table->foreign('facility_id')->references('facility_id')->on('facilities');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution');
    }
};

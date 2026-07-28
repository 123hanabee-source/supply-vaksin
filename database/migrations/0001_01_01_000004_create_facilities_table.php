<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->integer('facility_id')->primary();
            $table->string('facility_name', 64);
            $table->text('location_')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};

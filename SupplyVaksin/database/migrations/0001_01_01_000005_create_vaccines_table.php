<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccines', function (Blueprint $table) {
            $table->integer('vaccine_id')->primary();
            $table->integer('supplier_id')->nullable();
            $table->string('vaccine_name', 64);
            $table->date('expiry_date');

            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccines');
    }
};

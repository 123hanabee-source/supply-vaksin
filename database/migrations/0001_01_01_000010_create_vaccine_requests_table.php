<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vaccine_requests', function (Blueprint $table) {
            $table->integer('request_id')->primary();
            $table->integer('user_id');
            $table->integer('facility_id');
            $table->string('request_type', 20);
            $table->string('vaccine_name', 64)->nullable();
            $table->integer('quantity_needed')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('admin_notes')->nullable();
            $table->date('created_at');
        });

        DB::statement("ALTER TABLE vaccine_requests ADD CONSTRAINT vr_status_check CHECK (status IN ('pending', 'approved', 'rejected'))");
        DB::statement("ALTER TABLE vaccine_requests ADD CONSTRAINT vr_type_check CHECK (request_type IN ('new_vaccine', 'low_stock', 'other'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('vaccine_requests');
    }
};

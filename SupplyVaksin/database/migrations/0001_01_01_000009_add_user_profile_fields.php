<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email', 80)->nullable()->unique()->after('password');
            $table->string('sex', 10)->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('sex');
            $table->date('assigned_date')->nullable()->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'sex', 'date_of_birth', 'assigned_date']);
        });
    }
};

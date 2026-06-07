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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['owner', 'admin'])->default('admin')->after('password');
            $table->foreignId('pegawai_id')
                ->nullable()
                ->after('role')
                ->constrained('pegawai')
                ->nullOnDelete();
            $table->boolean('is_active')->default(true)->after('pegawai_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn(['role', 'pegawai_id', 'is_active']);
        });
    }
};

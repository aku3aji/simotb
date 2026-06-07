<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('pesan');
            $table->string('ikon')->default('bell');
            $table->string('warna')->default('brand');
            $table->string('tautan')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->index('dibaca_pada');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};

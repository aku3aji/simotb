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
        Schema::create('stok_mutasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barang')->restrictOnDelete();
            $table->enum('tipe', ['masuk', 'keluar', 'penyesuaian']);
            $table->enum('sumber', [
                'pembelian',
                'penjualan',
                'retur_penjualan',
                'stock_opname',
                'manual',
            ]);
            $table->unsignedBigInteger('sumber_id')->nullable();
            $table->integer('jumlah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->text('keterangan')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index(['barang_id', 'created_at']);
            $table->index(['sumber', 'sumber_id']);
        });

        Schema::create('stok_opname', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_opname')->unique();
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('tanggal');
        });

        Schema::create('stok_opname_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stok_opname_id')->constrained('stok_opname')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barang')->restrictOnDelete();
            $table->integer('stok_sistem');
            $table->integer('stok_fisik');
            $table->integer('selisih');
            $table->text('alasan')->nullable();
            $table->timestamps();

            $table->unique(['stok_opname_id', 'barang_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_opname_detail');
        Schema::dropIfExists('stok_opname');
        Schema::dropIfExists('stok_mutasi');
    }
};

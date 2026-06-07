<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Membuat kategori_id dan satuan_id nullable agar barang bisa dibuat
        // secara inline dari form transaksi tanpa harus memilih kategori/satuan
        DB::statement('ALTER TABLE barang MODIFY COLUMN kategori_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE barang MODIFY COLUMN satuan_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE barang MODIFY COLUMN kategori_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE barang MODIFY COLUMN satuan_id BIGINT UNSIGNED NOT NULL');
    }
};

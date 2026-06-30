<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pembelian', function (Blueprint $table) {
            $table->enum('tipe_pembayaran', ['tunai', 'kredit'])->default('tunai')->after('total');
            $table->decimal('dibayar', 15, 2)->default(0)->after('tipe_pembayaran');
            $table->decimal('sisa_utang', 15, 2)->default(0)->after('dibayar');
            $table->enum('status_pembayaran', ['lunas', 'belum_lunas', 'sebagian'])->default('lunas')->after('sisa_utang');
            $table->date('jatuh_tempo')->nullable()->after('status_pembayaran');

            $table->index(['tipe_pembayaran', 'status_pembayaran']);
        });

        // Backfill: transaksi pembelian lama dianggap tunai & lunas, dibayar = total.
        DB::table('pembelian')->update(['dibayar' => DB::raw('total')]);

        Schema::create('pembayaran_utang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->cascadeOnDelete();
            $table->date('tanggal');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->string('metode_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();

            $table->index('tanggal');
            $table->index(['pembelian_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran_utang');

        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropIndex(['tipe_pembayaran', 'status_pembayaran']);
            $table->dropColumn([
                'tipe_pembayaran',
                'dibayar',
                'sisa_utang',
                'status_pembayaran',
                'jatuh_tempo',
            ]);
        });
    }
};

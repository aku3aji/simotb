<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\Inventory\MutasiStokController;
use App\Http\Controllers\Inventory\StokOpnameController;
use App\Http\Controllers\Laporan\LaporanController;
use App\Http\Controllers\MasterData\BarangController;
use App\Http\Controllers\MasterData\KategoriController;
use App\Http\Controllers\MasterData\MerekController;
use App\Http\Controllers\MasterData\PelangganController;
use App\Http\Controllers\MasterData\SatuanController;
use App\Http\Controllers\MasterData\VendorController;
use App\Http\Controllers\Pegawai\AbsensiController;
use App\Http\Controllers\Pegawai\PegawaiController;
use App\Http\Controllers\Pengguna\UserController;
use App\Http\Controllers\Transaksi\PembayaranPiutangController;
use App\Http\Controllers\Transaksi\PembayaranUtangController;
use App\Http\Controllers\Transaksi\PembelianController;
use App\Http\Controllers\Transaksi\PenjualanController;
use App\Http\Controllers\Transaksi\ReturPenjualanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.store');

    Route::get('/lupa-password', [ForgotPasswordController::class, 'showForm'])
        ->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendLink'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showForm'])
        ->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])
        ->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::patch('profil', [ProfilController::class, 'update'])->name('profil.update');

    Route::get('notifikasi/data', [NotifikasiController::class, 'data'])->name('notifikasi.data');
    Route::post('notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');

    Route::prefix('master-data')
        ->as('master-data.')
        ->group(function () {
            Route::delete('kategori/bulk-destroy', [KategoriController::class, 'destroyBulk'])->name('kategori.bulk-destroy');
            Route::resource('kategori', KategoriController::class)->except(['show']);
            Route::delete('satuan/bulk-destroy', [SatuanController::class, 'destroyBulk'])->name('satuan.bulk-destroy');
            Route::resource('satuan', SatuanController::class)->except(['show']);
            Route::delete('merek/bulk-destroy', [MerekController::class, 'destroyBulk'])->name('merek.bulk-destroy');
            Route::resource('merek', MerekController::class)->except(['show']);
            Route::delete('vendor/bulk-destroy', [VendorController::class, 'destroyBulk'])->name('vendor.bulk-destroy');
            Route::resource('vendor', VendorController::class);
            Route::delete('pelanggan/bulk-destroy', [PelangganController::class, 'destroyBulk'])->name('pelanggan.bulk-destroy');
            Route::resource('pelanggan', PelangganController::class);
            Route::delete('barang/bulk-destroy', [BarangController::class, 'destroyBulk'])->name('barang.bulk-destroy');
            Route::resource('barang', BarangController::class);
        });

    Route::prefix('inventory')
        ->as('inventory.')
        ->group(function () {
            Route::resource('stok-opname', StokOpnameController::class)
                ->only(['index', 'create', 'store', 'show', 'destroy']);
            Route::get('mutasi-stok', [MutasiStokController::class, 'index'])->name('mutasi-stok.index');
            Route::get('mutasi-stok/{barang}', [MutasiStokController::class, 'show'])->name('mutasi-stok.show');
        });

    Route::prefix('transaksi')
        ->as('transaksi.')
        ->group(function () {
            Route::resource('stok-masuk', PembelianController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
                ->parameters(['stok-masuk' => 'pembelian']);

            Route::resource('stok-keluar', PenjualanController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update'])
                ->parameters(['stok-keluar' => 'penjualan']);

            Route::delete('pembayaran-piutang/bulk-destroy', [PembayaranPiutangController::class, 'destroyBulk'])->name('pembayaran-piutang.bulk-destroy');
            Route::get('pembayaran-piutang/{penjualan}/riwayat', [PembayaranPiutangController::class, 'show'])->name('pembayaran-piutang.show');
            Route::resource('pembayaran-piutang', PembayaranPiutangController::class)
                ->except(['show']);

            Route::delete('pembayaran-utang/bulk-destroy', [PembayaranUtangController::class, 'destroyBulk'])->name('pembayaran-utang.bulk-destroy');
            Route::get('pembayaran-utang/{pembelian}/riwayat', [PembayaranUtangController::class, 'show'])->name('pembayaran-utang.show');
            Route::resource('pembayaran-utang', PembayaranUtangController::class)
                ->except(['show'])
                ->parameters(['pembayaran-utang' => 'pembayaranUtang']);

            Route::resource('retur-stok-keluar', ReturPenjualanController::class)
                ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
                ->parameters(['retur-stok-keluar' => 'returPenjualan']);
        });

    Route::prefix('pegawai')
        ->as('pegawai.')
        ->group(function () {
            // Manajemen data pegawai: khusus Owner.
            Route::middleware('owner')->group(function () {
                Route::delete('pegawai/bulk-destroy', [PegawaiController::class, 'destroyBulk'])->name('pegawai.bulk-destroy');
                Route::resource('pegawai', PegawaiController::class)->except(['show']);
            });

            // Absensi: dapat diakses Owner maupun Admin.
            Route::get('absensi/catat-massal', [AbsensiController::class, 'cataMassal'])->name('absensi.catat-massal');
            Route::post('absensi/catat-massal', [AbsensiController::class, 'storeMassal'])->name('absensi.store-massal');
            Route::delete('absensi/bulk-destroy', [AbsensiController::class, 'destroyBulk'])->name('absensi.bulk-destroy');
            Route::resource('absensi', AbsensiController::class)->except(['show', 'create', 'store']);
        });

    Route::middleware('owner')
        ->prefix('pengguna')
        ->as('pengguna.')
        ->group(function () {
            Route::delete('user/bulk-destroy', [UserController::class, 'destroyBulk'])->name('user.bulk-destroy');
            Route::resource('user', UserController::class)->except(['show']);
        });

    Route::middleware('owner')
        ->prefix('laporan')
        ->as('laporan.')
        ->group(function () {
            Route::get('stok', [LaporanController::class, 'stok'])->name('stok');
            Route::get('stok-masuk', [LaporanController::class, 'pembelian'])->name('stok-masuk');
            Route::get('stok-keluar', [LaporanController::class, 'penjualan'])->name('stok-keluar');
            Route::get('piutang', [LaporanController::class, 'piutang'])->name('piutang');
            Route::get('mutasi-stok', [LaporanController::class, 'mutasiStok'])->name('mutasi-stok');
            Route::get('stok-opname', [LaporanController::class, 'stokOpname'])->name('stok-opname');
            Route::get('absensi', [LaporanController::class, 'absensi'])->name('absensi');
        });
});

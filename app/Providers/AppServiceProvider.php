<?php

namespace App\Providers;

use App\Models\Absensi;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Merek;
use App\Models\Pelanggan;
use App\Models\Pegawai;
use App\Models\PembayaranPiutang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\ReturPenjualan;
use App\Models\Satuan;
use App\Models\StokOpname;
use App\Models\User;
use App\Models\Vendor;
use App\Observers\ModelNotifikasiObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->registerNotifikasiObservers();
    }

    private function registerNotifikasiObservers(): void
    {
        $this->attachObserver(Barang::class, new ModelNotifikasiObserver(
            'Barang', 'package', 'brand',
            fn ($m) => $m->nama . ' (' . $m->kode_barang . ')',
            fn ($m) => route('master-data.barang.show', $m->id),
        ));

        $this->attachObserver(Penjualan::class, new ModelNotifikasiObserver(
            'Penjualan', 'shopping-cart', 'brand',
            fn ($m) => $m->nomor_penjualan,
            fn ($m) => route('transaksi.stok-keluar.index'),
        ));

        $this->attachObserver(Pembelian::class, new ModelNotifikasiObserver(
            'Pembelian', 'receipt', 'brand',
            fn ($m) => $m->nomor_pembelian,
            fn ($m) => route('transaksi.stok-masuk.index'),
        ));

        $this->attachObserver(Pelanggan::class, new ModelNotifikasiObserver(
            'Pelanggan', 'user', 'brand',
            fn ($m) => $m->nama,
            fn ($m) => route('master-data.pelanggan.index'),
        ));

        $this->attachObserver(Vendor::class, new ModelNotifikasiObserver(
            'Vendor', 'truck', 'brand',
            fn ($m) => $m->nama,
            fn ($m) => route('master-data.vendor.index'),
        ));

        $this->attachObserver(Kategori::class, new ModelNotifikasiObserver(
            'Kategori', 'tag', 'brand',
            fn ($m) => $m->nama,
            fn ($m) => route('master-data.kategori.index'),
        ));

        $this->attachObserver(Satuan::class, new ModelNotifikasiObserver(
            'Satuan', 'ruler', 'brand',
            fn ($m) => $m->nama,
            fn ($m) => route('master-data.satuan.index'),
        ));

        $this->attachObserver(Merek::class, new ModelNotifikasiObserver(
            'Merek', 'bookmark', 'brand',
            fn ($m) => $m->nama,
            fn ($m) => route('master-data.merek.index'),
        ));

        $this->attachObserver(Pegawai::class, new ModelNotifikasiObserver(
            'Pegawai', 'user-check', 'brand',
            fn ($m) => $m->nama,
            fn ($m) => route('pegawai.pegawai.index'),
        ));

        $this->attachObserver(Absensi::class, new ModelNotifikasiObserver(
            'Absensi', 'clock', 'brand',
            fn ($m) => 'Data absensi ' . optional($m->tanggal)->format('d/m/Y'),
            fn ($m) => route('pegawai.absensi.index'),
        ));

        $this->attachObserver(StokOpname::class, new ModelNotifikasiObserver(
            'Stok Opname', 'boxes', 'warning',
            fn ($m) => 'Opname stok ' . optional($m->tanggal)->format('d/m/Y'),
            fn ($m) => route('inventory.stok-opname.index'),
        ));

        $this->attachObserver(ReturPenjualan::class, new ModelNotifikasiObserver(
            'Retur Penjualan', 'rotate-ccw', 'danger',
            fn ($m) => $m->nomor_retur ?? ('Retur #' . $m->id),
            fn ($m) => route('transaksi.retur-stok-keluar.index'),
        ));

        $this->attachObserver(PembayaranPiutang::class, new ModelNotifikasiObserver(
            'Pembayaran Piutang', 'wallet', 'success',
            fn ($m) => 'Pembayaran Rp ' . number_format($m->jumlah_bayar ?? 0, 0, ',', '.'),
            fn ($m) => route('transaksi.pembayaran-piutang.index'),
        ));

        $this->attachObserver(User::class, new ModelNotifikasiObserver(
            'User', 'shield', 'danger',
            fn ($m) => $m->name . ' (' . $m->role . ')',
            fn ($m) => route('pengguna.user.index'),
        ));
    }

    private function attachObserver(string $model, ModelNotifikasiObserver $observer): void
    {
        $model::created([$observer, 'created']);
        $model::updated([$observer, 'updated']);
        $model::deleted([$observer, 'deleted']);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\StokMutasi;
use App\Models\Barang;
use App\Models\Vendor;
use App\Models\Pelanggan;
use App\Models\User;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        $vendors = Vendor::all();
        $pelanggans = Pelanggan::all();
        $barangs = Barang::all();

        if ($vendors->isEmpty() || $pelanggans->isEmpty() || $barangs->isEmpty()) return;

        // Dummy Pembelian
        for ($i = 1; $i <= 10; $i++) {
            $tanggal = fake()->dateTimeBetween('-3 months', 'now');
            $pembelian = Pembelian::create([
                'nomor_pembelian' => 'PB-' . $tanggal->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'vendor_id' => $vendors->random()->id,
                'tanggal' => $tanggal,
                'total' => 0,
                'user_id' => $admin->id,
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ]);

            $totalPembelian = 0;
            $items = $barangs->random(rand(2, 5));
            foreach ($items as $barang) {
                $jumlah = rand(10, 50);
                $subtotal = $jumlah * $barang->harga_beli;
                $totalPembelian += $subtotal;

                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'harga_beli' => $barang->harga_beli,
                    'subtotal' => $subtotal,
                ]);

                $stokSebelum = $barang->stok;
                $stokSesudah = $stokSebelum + $jumlah;

                StokMutasi::insert([
                    'barang_id' => $barang->id,
                    'tipe' => 'masuk',
                    'sumber' => 'pembelian',
                    'sumber_id' => $pembelian->id,
                    'jumlah' => $jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $admin->id,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]);

                $barang->update(['stok' => $stokSesudah]);
            }
            $pembelian->update(['total' => $totalPembelian]);
        }

        // Dummy Penjualan
        for ($i = 1; $i <= 20; $i++) {
            $tanggal = fake()->dateTimeBetween('-1 months', 'now');
            $tipePembayaran = fake()->randomElement(['tunai', 'kredit']);
            
            $penjualan = Penjualan::create([
                'nomor_penjualan' => 'PJ-' . $tanggal->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'pelanggan_id' => $pelanggans->random()->id,
                'tanggal' => $tanggal,
                'tipe_pembayaran' => $tipePembayaran,
                'total' => 0,
                'dibayar' => 0,
                'sisa_piutang' => 0,
                'status_pembayaran' => 'belum_lunas',
                'user_id' => $admin->id,
                'created_at' => $tanggal,
                'updated_at' => $tanggal,
            ]);

            $totalPenjualan = 0;
            $items = $barangs->random(rand(1, 4));
            foreach ($items as $barang) {
                if ($barang->stok < 1) continue;

                $jumlah = rand(1, min(5, $barang->stok));
                $subtotal = $jumlah * $barang->harga_jual;
                $totalPenjualan += $subtotal;

                PenjualanDetail::create([
                    'penjualan_id' => $penjualan->id,
                    'barang_id' => $barang->id,
                    'jumlah' => $jumlah,
                    'harga_jual' => $barang->harga_jual,
                    'subtotal' => $subtotal,
                ]);

                $stokSebelum = $barang->stok;
                $stokSesudah = $stokSebelum - $jumlah;

                StokMutasi::insert([
                    'barang_id' => $barang->id,
                    'tipe' => 'keluar',
                    'sumber' => 'penjualan',
                    'sumber_id' => $penjualan->id,
                    'jumlah' => $jumlah,
                    'stok_sebelum' => $stokSebelum,
                    'stok_sesudah' => $stokSesudah,
                    'user_id' => $admin->id,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]);

                $barang->update(['stok' => $stokSesudah]);
            }

            if ($totalPenjualan == 0) {
                $penjualan->delete();
                continue;
            }

            $dibayar = $tipePembayaran === 'tunai' ? $totalPenjualan : 0;
            $sisaPiutang = $totalPenjualan - $dibayar;
            $status = $tipePembayaran === 'tunai' ? 'lunas' : 'belum_lunas';

            $penjualan->update([
                'total' => $totalPenjualan,
                'dibayar' => $dibayar,
                'sisa_piutang' => $sisaPiutang,
                'status_pembayaran' => $status,
            ]);
        }
    }
}

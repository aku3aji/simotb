<?php

namespace Database\Seeders;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Merek;
use App\Models\Pegawai;
use App\Models\Pelanggan;
use App\Models\Satuan;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = collect([
            ['nama' => 'Material Dasar', 'deskripsi' => 'Semen, pasir, batu, dan material struktur dasar.'],
            ['nama' => 'Finishing & Cat', 'deskripsi' => 'Cat, kuas, dempul, dan kebutuhan finishing.'],
            ['nama' => 'Hardware', 'deskripsi' => 'Paku, sekrup, engsel, dan perlengkapan kecil.'],
            ['nama' => 'Plumbing', 'deskripsi' => 'Pipa, sambungan, kran, dan perlengkapan air.'],
            ['nama' => 'Listrik', 'deskripsi' => 'Kabel, saklar, stop kontak, dan perlengkapan listrik.'],
        ])->mapWithKeys(fn (array $item) => [
            $item['nama'] => Kategori::updateOrCreate(
                ['nama' => $item['nama']],
                ['deskripsi' => $item['deskripsi']]
            ),
        ]);

        $satuan = collect([
            ['nama' => 'Sak', 'singkatan' => 'sak'],
            ['nama' => 'Pcs', 'singkatan' => 'pcs'],
            ['nama' => 'Batang', 'singkatan' => 'btg'],
            ['nama' => 'Dus', 'singkatan' => 'dus'],
            ['nama' => 'Galon', 'singkatan' => 'gal'],
            ['nama' => 'Roll', 'singkatan' => 'roll'],
        ])->mapWithKeys(fn (array $item) => [
            $item['nama'] => Satuan::updateOrCreate(
                ['nama' => $item['nama']],
                ['singkatan' => $item['singkatan']]
            ),
        ]);

        $merek = collect([
            'Tiga Roda',
            'Dulux',
            'Kuda Terbang',
            'Wavin',
            'Nippon Paint',
            'Eterna',
            'Broco',
        ])->mapWithKeys(fn (string $nama) => [
            $nama => Merek::updateOrCreate(
                ['nama' => $nama],
                ['deskripsi' => 'Merek ' . $nama . ' untuk kebutuhan toko.']
            ),
        ]);

        foreach ([
            [
                'nama' => 'CV Bangun Sentosa',
                'telepon' => '081234567801',
                'alamat' => 'Jl. Industri No. 12',
                'email' => 'bangunsentosa@example.com',
                'kontak_person' => 'Pak Ahmad',
            ],
            [
                'nama' => 'PT Distributor Semen Jaya',
                'telepon' => '081234567802',
                'alamat' => 'Jl. Raya Gudang No. 8',
                'email' => 'semenjaya@example.com',
                'kontak_person' => 'Bu Ratna',
            ],
            [
                'nama' => 'UD Sumber Cat',
                'telepon' => '081234567803',
                'alamat' => 'Jl. Warna No. 5',
                'email' => 'sumbercat@example.com',
                'kontak_person' => 'Pak Dimas',
            ],
        ] as $item) {
            Vendor::updateOrCreate(['nama' => $item['nama']], $item);
        }

        foreach ([
            [
                'nama' => 'Pelanggan Umum',
                'telepon' => null,
                'alamat' => null,
                'email' => null,
            ],
            [
                'nama' => 'Bpk. Budi Santoso',
                'telepon' => '081298765401',
                'alamat' => 'Jl. Melati No. 21',
                'email' => 'budi@example.com',
            ],
            [
                'nama' => 'PT Bangun Sejahtera',
                'telepon' => '081298765402',
                'alamat' => 'Jl. Proyek No. 88',
                'email' => 'bangunsejahtera@example.com',
            ],
            [
                'nama' => 'Ibu Sari',
                'telepon' => '081298765403',
                'alamat' => 'Jl. Mawar No. 3',
                'email' => 'sari@example.com',
            ],
        ] as $item) {
            Pelanggan::updateOrCreate(['nama' => $item['nama']], $item);
        }

        foreach ([
            [
                'nama' => 'Owner Sumber Alam',
                'jabatan' => 'Owner',
                'telepon' => '081111111111',
                'alamat' => 'Jl. Toko No. 1',
                'tanggal_masuk' => '2024-01-01',
                'status' => Pegawai::STATUS_AKTIF,
            ],
            [
                'nama' => 'Admin Kasir',
                'jabatan' => 'Admin/Kasir',
                'telepon' => '082222222222',
                'alamat' => 'Jl. Toko No. 2',
                'tanggal_masuk' => '2024-02-01',
                'status' => Pegawai::STATUS_AKTIF,
            ],
            [
                'nama' => 'Bagus Gudang',
                'jabatan' => 'Staff Gudang',
                'telepon' => '083333333333',
                'alamat' => 'Jl. Toko No. 3',
                'tanggal_masuk' => '2024-03-01',
                'status' => Pegawai::STATUS_AKTIF,
            ],
        ] as $item) {
            Pegawai::updateOrCreate(['nama' => $item['nama']], $item);
        }

        foreach ([
            [
                'kode_barang' => 'MAT-SMN-001',
                'nama' => 'Semen Portland 50kg',
                'kategori' => 'Material Dasar',
                'satuan' => 'Sak',
                'merek' => 'Tiga Roda',
                'harga_beli' => 56000,
                'harga_jual' => 65000,
                'stok' => 120,
                'stok_minimum' => 20,
            ],
            [
                'kode_barang' => 'FIN-CAT-045',
                'nama' => 'Cat Tembok Putih 5kg',
                'kategori' => 'Finishing & Cat',
                'satuan' => 'Galon',
                'merek' => 'Dulux',
                'harga_beli' => 128000,
                'harga_jual' => 145000,
                'stok' => 12,
                'stok_minimum' => 15,
            ],
            [
                'kode_barang' => 'HDW-PKU-012',
                'nama' => 'Paku Beton 5cm',
                'kategori' => 'Hardware',
                'satuan' => 'Dus',
                'merek' => 'Kuda Terbang',
                'harga_beli' => 20000,
                'harga_jual' => 25000,
                'stok' => 45,
                'stok_minimum' => 10,
            ],
            [
                'kode_barang' => 'PLM-PVC-008',
                'nama' => 'Pipa PVC 1/2 inch AW',
                'kategori' => 'Plumbing',
                'satuan' => 'Batang',
                'merek' => 'Wavin',
                'harga_beli' => 27000,
                'harga_jual' => 32000,
                'stok' => 0,
                'stok_minimum' => 8,
            ],
            [
                'kode_barang' => 'LST-KBL-021',
                'nama' => 'Kabel NYA 1.5mm',
                'kategori' => 'Listrik',
                'satuan' => 'Roll',
                'merek' => 'Eterna',
                'harga_beli' => 165000,
                'harga_jual' => 185000,
                'stok' => 18,
                'stok_minimum' => 5,
            ],
        ] as $item) {
            Barang::updateOrCreate(
                ['kode_barang' => $item['kode_barang']],
                [
                    'nama' => $item['nama'],
                    'kategori_id' => $kategori[$item['kategori']]->id,
                    'satuan_id' => $satuan[$item['satuan']]->id,
                    'merek_id' => $merek[$item['merek']]->id,
                    'harga_beli' => $item['harga_beli'],
                    'harga_jual' => $item['harga_jual'],
                    'stok' => $item['stok'],
                    'stok_minimum' => $item['stok_minimum'],
                    'deskripsi' => 'Data dummy barang untuk pengujian sistem.',
                    'is_active' => true,
                ]
            );
        }
    }
}

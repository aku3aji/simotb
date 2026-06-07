<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Barang;
use App\Models\StokMutasi;

trait InteractsWithStok
{
    protected function catatMutasiStok(
        Barang $barang,
        string $tipe,
        string $sumber,
        ?int $sumberId,
        int $jumlah,
        int $stokSebelum,
        int $stokSesudah,
        int $userId,
        ?string $keterangan = null
    ): void {
        if ($jumlah <= 0 || $stokSebelum === $stokSesudah) {
            return;
        }

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tipe' => $tipe,
            'sumber' => $sumber,
            'sumber_id' => $sumberId,
            'jumlah' => $jumlah,
            'stok_sebelum' => $stokSebelum,
            'stok_sesudah' => $stokSesudah,
            'keterangan' => $keterangan,
            'user_id' => $userId,
        ]);
    }
}

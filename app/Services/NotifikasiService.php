<?php

namespace App\Services;

use App\Models\Notifikasi;

class NotifikasiService
{
    public static function catat(
        string $judul,
        string $pesan,
        string $ikon = 'bell',
        string $warna = 'brand',
        ?string $tautan = null,
    ): void {
        if (app()->runningInConsole()) {
            return;
        }

        Notifikasi::create([
            'judul'      => $judul,
            'pesan'      => $pesan,
            'ikon'       => $ikon,
            'warna'      => $warna,
            'tautan'     => $tautan,
            'created_by' => auth()->user()?->name ?? 'Sistem',
        ]);
    }
}

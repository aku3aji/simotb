<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    public const KEY_MAKS_HARI_JATUH_TEMPO = 'maks_hari_jatuh_tempo';
    public const DEFAULT_MAKS_HARI_JATUH_TEMPO = 30;

    protected $table = 'pengaturan';

    protected $fillable = [
        'kunci',
        'nilai',
    ];

    public static function get(string $kunci, mixed $default = null): mixed
    {
        $nilai = static::query()->where('kunci', $kunci)->value('nilai');

        return $nilai ?? $default;
    }

    public static function set(string $kunci, mixed $nilai): void
    {
        static::query()->updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => (string) $nilai],
        );
    }

    /**
     * Batas maksimal tenor jatuh tempo (dalam hari) untuk penjualan kredit.
     * Selalu mengembalikan angka positif; jatuh ke nilai default bila belum diatur.
     */
    public static function maksHariJatuhTempo(): int
    {
        $nilai = (int) static::get(self::KEY_MAKS_HARI_JATUH_TEMPO, self::DEFAULT_MAKS_HARI_JATUH_TEMPO);

        return $nilai > 0 ? $nilai : self::DEFAULT_MAKS_HARI_JATUH_TEMPO;
    }
}

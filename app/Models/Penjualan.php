<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

    public const TIPE_TUNAI = 'tunai';
    public const TIPE_KREDIT = 'kredit';

    public const STATUS_LUNAS = 'lunas';
    public const STATUS_BELUM_LUNAS = 'belum_lunas';
    public const STATUS_SEBAGIAN = 'sebagian';

    protected $table = 'penjualan';

    protected $fillable = [
        'nomor_penjualan',
        'pelanggan_id',
        'tanggal',
        'tipe_pembayaran',
        'total',
        'dibayar',
        'sisa_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jatuh_tempo' => 'date',
        'total' => 'decimal:2',
        'dibayar' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function pembayaranPiutang()
    {
        return $this->hasMany(PembayaranPiutang::class);
    }

    public function returPenjualan()
    {
        return $this->hasMany(ReturPenjualan::class);
    }

    public function scopeKredit(Builder $query): Builder
    {
        return $query->where('tipe_pembayaran', self::TIPE_KREDIT);
    }

    public function scopeBelumLunas(Builder $query): Builder
    {
        return $query->where('status_pembayaran', '!=', self::STATUS_LUNAS);
    }
}

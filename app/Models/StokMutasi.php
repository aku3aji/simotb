<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StokMutasi extends Model
{
    use HasFactory;

    public const TIPE_MASUK = 'masuk';
    public const TIPE_KELUAR = 'keluar';
    public const TIPE_PENYESUAIAN = 'penyesuaian';

    public const SUMBER_PEMBELIAN = 'pembelian';
    public const SUMBER_PENJUALAN = 'penjualan';
    public const SUMBER_RETUR_PENJUALAN = 'retur_penjualan';
    public const SUMBER_STOCK_OPNAME = 'stock_opname';
    public const SUMBER_MANUAL = 'manual';

    protected $table = 'stok_mutasi';

    protected $fillable = [
        'barang_id',
        'tipe',
        'sumber',
        'sumber_id',
        'jumlah',
        'stok_sebelum',
        'stok_sesudah',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'stok_sebelum' => 'integer',
        'stok_sesudah' => 'integer',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

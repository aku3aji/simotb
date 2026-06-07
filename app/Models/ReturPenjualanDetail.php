<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenjualanDetail extends Model
{
    use HasFactory;

    public const KONDISI_BAIK = 'baik';
    public const KONDISI_RUSAK = 'rusak';

    protected $table = 'retur_penjualan_detail';

    protected $fillable = [
        'retur_penjualan_id',
        'barang_id',
        'jumlah',
        'harga_jual',
        'subtotal',
        'kondisi_barang',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga_jual' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function returPenjualan()
    {
        return $this->belongsTo(ReturPenjualan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}

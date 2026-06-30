<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranUtang extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_utang';

    protected $fillable = [
        'pembelian_id',
        'tanggal',
        'jumlah_bayar',
        'metode_pembayaran',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_bayar' => 'decimal:2',
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    use HasFactory;

    protected $table = 'retur_penjualan';

    protected $fillable = [
        'nomor_retur',
        'penjualan_id',
        'tanggal',
        'total_retur',
        'alasan',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_retur' => 'decimal:2',
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(ReturPenjualanDetail::class);
    }
}

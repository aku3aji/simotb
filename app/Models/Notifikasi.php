<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'judul',
        'pesan',
        'ikon',
        'warna',
        'tautan',
        'created_by',
        'dibaca_pada',
    ];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    public function scopeBelumDibaca(Builder $query)
    {
        return $query->whereNull('dibaca_pada');
    }

    public function sudahDibaca(): bool
    {
        return $this->dibaca_pada !== null;
    }
}

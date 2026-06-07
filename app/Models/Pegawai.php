<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    public const STATUS_AKTIF = 'aktif';
    public const STATUS_NONAKTIF = 'nonaktif';

    protected $table = 'pegawai';

    protected $fillable = [
        'nama',
        'jabatan',
        'telepon',
        'alamat',
        'tanggal_masuk',
        'gaji_harian',
        'status',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'gaji_harian'   => 'integer',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}

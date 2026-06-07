<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendor';

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'email',
        'kontak_person',
    ];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}

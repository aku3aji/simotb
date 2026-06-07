<?php

namespace App\Http\Requests\Pegawai;

use App\Http\Requests\BaseFormRequest;
use App\Models\Pegawai;
use Illuminate\Validation\Rule;

class StorePegawaiRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['nullable', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'tanggal_masuk' => ['nullable', 'date'],
            'gaji_harian' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', Rule::in([Pegawai::STATUS_AKTIF, Pegawai::STATUS_NONAKTIF])],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama pegawai',
            'jabatan' => 'jabatan',
            'telepon' => 'telepon',
            'alamat' => 'alamat',
            'tanggal_masuk' => 'tanggal masuk',
            'gaji_harian' => 'gaji harian',
            'status' => 'status',
        ];
    }
}

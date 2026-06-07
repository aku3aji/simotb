<?php

namespace App\Http\Requests\Pegawai;

use Illuminate\Validation\Rule;

class UpdateAbsensiRequest extends StoreAbsensiRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'exists:pegawai,id'],
            'tanggal' => [
                'required',
                'date',
                Rule::unique('absensi', 'tanggal')
                    ->where(fn ($query) => $query->where('pegawai_id', $this->input('pegawai_id')))
                    ->ignore($this->routeModelId('absensi')),
            ],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
            'status' => [
                'required',
                Rule::in(['hadir', 'izin', 'sakit', 'alpha']),
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

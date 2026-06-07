<?php

namespace App\Http\Requests\Pegawai;

use App\Http\Requests\BaseFormRequest;
use App\Models\Absensi;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAbsensiRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'pegawai_id' => ['required', 'exists:pegawai,id'],
            'tanggal' => [
                'required',
                'date',
                Rule::unique('absensi', 'tanggal')->where(
                    fn ($query) => $query->where('pegawai_id', $this->input('pegawai_id'))
                ),
            ],
            'jam_masuk' => ['nullable', 'date_format:H:i'],
            'jam_keluar' => ['nullable', 'date_format:H:i'],
            'status' => [
                'required',
                Rule::in([
                    Absensi::STATUS_HADIR,
                    Absensi::STATUS_IZIN,
                    Absensi::STATUS_SAKIT,
                    Absensi::STATUS_ALPHA,
                ]),
            ],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (
                $this->filled('jam_masuk') &&
                $this->filled('jam_keluar') &&
                $this->input('jam_keluar') < $this->input('jam_masuk')
            ) {
                $validator->errors()->add('jam_keluar', 'Jam keluar harus sama atau setelah jam masuk.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'pegawai_id' => 'pegawai',
            'tanggal' => 'tanggal absensi',
            'jam_masuk' => 'jam masuk',
            'jam_keluar' => 'jam keluar',
            'status' => 'status kehadiran',
            'keterangan' => 'keterangan',
        ];
    }
}

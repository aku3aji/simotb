<?php

namespace App\Http\Requests\MasterData;

use App\Http\Requests\BaseFormRequest;

class StoreVendorRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'email' => ['nullable', 'email', 'max:255'],
            'kontak_person' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama vendor',
            'telepon' => 'telepon',
            'alamat' => 'alamat',
            'email' => 'email',
            'kontak_person' => 'kontak person',
        ];
    }
}

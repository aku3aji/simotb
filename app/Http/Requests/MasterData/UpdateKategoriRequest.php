<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Validation\Rule;

class UpdateKategoriRequest extends StoreKategoriRequest
{
    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kategori', 'nama')->ignore($this->routeModelId('kategori')),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

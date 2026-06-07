<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Validation\Rule;

class UpdateSatuanRequest extends StoreSatuanRequest
{
    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('satuan', 'nama')->ignore($this->routeModelId('satuan')),
            ],
            'singkatan' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('satuan', 'singkatan')->ignore($this->routeModelId('satuan')),
            ],
        ];
    }
}

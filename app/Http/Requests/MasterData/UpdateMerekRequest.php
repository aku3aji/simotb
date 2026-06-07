<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Validation\Rule;

class UpdateMerekRequest extends StoreMerekRequest
{
    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique('merek', 'nama')->ignore($this->routeModelId('merek')),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

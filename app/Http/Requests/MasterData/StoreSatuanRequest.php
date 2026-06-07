<?php

namespace App\Http\Requests\MasterData;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreSatuanRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', Rule::unique('satuan', 'nama')],
            'singkatan' => ['nullable', 'string', 'max:50', Rule::unique('satuan', 'singkatan')],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama satuan',
            'singkatan' => 'singkatan',
        ];
    }
}

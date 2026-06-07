<?php

namespace App\Http\Requests\MasterData;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreMerekRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255', Rule::unique('merek', 'nama')],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nama' => 'nama merek',
            'deskripsi' => 'deskripsi',
        ];
    }
}

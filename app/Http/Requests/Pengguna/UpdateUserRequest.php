<?php

namespace App\Http\Requests\Pengguna;

use App\Models\User;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends StoreUserRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->routeModelId('user')),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_OWNER, User::ROLE_ADMIN])],
            'pegawai_id' => [
                'nullable',
                'exists:pegawai,id',
                Rule::unique('users', 'pegawai_id')->ignore($this->routeModelId('user')),
            ],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests\Pengguna;

use App\Http\Requests\BaseFormRequest;
use App\Models\User;
use Illuminate\Validation\Rule;

class StoreUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_OWNER, User::ROLE_ADMIN])],
            'pegawai_id' => ['nullable', 'exists:pegawai,id', Rule::unique('users', 'pegawai_id')],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama pengguna',
            'email' => 'email',
            'password' => 'password',
            'password_confirmation' => 'konfirmasi password',
            'role' => 'role',
            'pegawai_id' => 'pegawai',
            'is_active' => 'status aktif',
        ];
    }
}

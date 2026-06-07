<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function routeModelId(array|string $keys): mixed
    {
        foreach ((array) $keys as $key) {
            $value = $this->route($key);

            if ($value === null) {
                continue;
            }

            if (is_object($value) && method_exists($value, 'getKey')) {
                return $value->getKey();
            }

            return $value;
        }

        return null;
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'required_if' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'integer' => ':attribute harus berupa angka bulat.',
            'numeric' => ':attribute harus berupa angka.',
            'boolean' => ':attribute harus berupa nilai benar atau salah.',
            'array' => ':attribute harus berupa daftar data.',
            'date' => ':attribute harus berupa tanggal yang valid.',
            'date_format' => ':attribute tidak sesuai format yang ditentukan.',
            'email' => ':attribute harus berupa email yang valid.',
            'exists' => ':attribute tidak valid.',
            'unique' => ':attribute sudah digunakan.',
            'distinct' => ':attribute tidak boleh duplikat.',
            'confirmed' => 'Konfirmasi :attribute tidak sesuai.',
            'in' => ':attribute tidak valid.',
            'min.string' => ':attribute minimal :min karakter.',
            'min.numeric' => ':attribute minimal :min.',
            'min.array' => ':attribute minimal :min item.',
            'max.string' => ':attribute maksimal :max karakter.',
            'max.numeric' => ':attribute maksimal :max.',
            'max.array' => ':attribute maksimal :max item.',
            'gt' => ':attribute harus lebih besar dari :value.',
            'gte' => ':attribute minimal :value.',
            'lte' => ':attribute tidak boleh lebih besar dari :value.',
            'after_or_equal' => ':attribute harus sama atau setelah :date.',
        ];
    }
}

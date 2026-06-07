<?php

namespace App\Http\Requests\MasterData;

use Illuminate\Validation\Rule;

class UpdateBarangRequest extends StoreBarangRequest
{
    public function rules(): array
    {
        return [
            'kode_barang' => [
                'required',
                'string',
                'max:100',
                Rule::unique('barang', 'kode_barang')->ignore($this->routeModelId('barang')),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori,id'],
            'satuan_id' => ['required', 'exists:satuan,id'],
            'merek_id' => ['nullable', 'exists:merek,id'],
            'harga_beli' => ['required', 'numeric', 'gte:0'],
            'harga_jual' => ['required', 'numeric', 'gte:0'],
            'stok' => ['nullable', 'integer', 'gte:0'],
            'stok_minimum' => ['required', 'integer', 'gte:0'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

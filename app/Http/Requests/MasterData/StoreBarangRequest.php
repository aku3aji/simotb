<?php

namespace App\Http\Requests\MasterData;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreBarangRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'kode_barang' => ['required', 'string', 'max:100', Rule::unique('barang', 'kode_barang')],
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

    public function attributes(): array
    {
        return [
            'kode_barang' => 'kode barang',
            'nama' => 'nama barang',
            'kategori_id' => 'kategori',
            'satuan_id' => 'satuan',
            'merek_id' => 'merek',
            'harga_beli' => 'harga beli',
            'harga_jual' => 'harga jual',
            'stok' => 'stok',
            'stok_minimum' => 'stok minimum',
            'deskripsi' => 'deskripsi',
            'is_active' => 'status aktif',
        ];
    }
}

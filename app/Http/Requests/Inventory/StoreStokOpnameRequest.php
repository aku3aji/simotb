<?php

namespace App\Http\Requests\Inventory;

use App\Http\Requests\BaseFormRequest;

class StoreStokOpnameRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'distinct', 'exists:barang,id'],
            'detail.*.stok_fisik' => ['nullable', 'integer', 'gte:0'],
            'detail.*.alasan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor_opname' => 'nomor opname',
            'tanggal' => 'tanggal opname',
            'catatan' => 'catatan',
            'detail' => 'detail opname',
            'detail.*.barang_id' => 'barang',
            'detail.*.stok_fisik' => 'stok fisik',
            'detail.*.alasan' => 'alasan',
        ];
    }
}

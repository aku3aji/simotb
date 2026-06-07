<?php

namespace App\Http\Requests\Transaksi;

use App\Models\ReturPenjualanDetail;
use Illuminate\Validation\Rule;

class UpdateReturPenjualanRequest extends StoreReturPenjualanRequest
{
    public function rules(): array
    {
        return [
            'nomor_retur' => [
                'required',
                'string',
                'max:100',
                Rule::unique('retur_penjualan', 'nomor_retur')->ignore($this->routeModelId(['retur_penjualan', 'returPenjualan'])),
            ],
            'penjualan_id' => ['required', 'exists:penjualan,id'],
            'tanggal' => ['required', 'date'],
            'alasan' => ['nullable', 'string', 'max:1000'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'distinct', 'exists:barang,id'],
            'detail.*.jumlah' => ['required', 'integer', 'gte:1'],
            'detail.*.harga_jual' => ['required', 'numeric', 'gte:0'],
            'detail.*.kondisi_barang' => ['required', Rule::in([ReturPenjualanDetail::KONDISI_BAIK, ReturPenjualanDetail::KONDISI_RUSAK])],
        ];
    }
}

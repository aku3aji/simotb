<?php

namespace App\Http\Requests\Transaksi;

use App\Models\Penjualan;
use Illuminate\Validation\Rule;

class UpdatePenjualanRequest extends StorePenjualanRequest
{
    public function rules(): array
    {
        return [
            'nomor_penjualan' => [
                'required',
                'string',
                'max:100',
                Rule::unique('penjualan', 'nomor_penjualan')->ignore($this->routeModelId('penjualan')),
            ],
            'pelanggan_id' => ['nullable', 'required_if:tipe_pembayaran,' . Penjualan::TIPE_KREDIT, 'exists:pelanggan,id'],
            'tanggal' => ['required', 'date'],
            'tipe_pembayaran' => ['required', Rule::in([Penjualan::TIPE_TUNAI, Penjualan::TIPE_KREDIT])],
            'dibayar' => ['required', 'numeric', 'gte:0'],
            'jatuh_tempo' => $this->jatuhTempoRules(),
            'catatan' => ['nullable', 'string', 'max:1000'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'distinct', 'exists:barang,id'],
            'detail.*.jumlah' => ['required', 'integer', 'gte:1'],
            'detail.*.harga_jual' => ['required', 'numeric', 'gt:0'],
            'newly_created_barang_ids' => ['nullable', 'array'],
            'newly_created_barang_ids.*' => ['integer'],
        ];
    }
}

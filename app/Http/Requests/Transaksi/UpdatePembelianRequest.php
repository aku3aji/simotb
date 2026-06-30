<?php

namespace App\Http\Requests\Transaksi;

use App\Models\Pembelian;
use Illuminate\Validation\Rule;

class UpdatePembelianRequest extends StorePembelianRequest
{
    public function rules(): array
    {
        return [
            'nomor_pembelian' => [
                'required',
                'string',
                'max:100',
                Rule::unique('pembelian', 'nomor_pembelian')->ignore($this->routeModelId('pembelian')),
            ],
            'vendor_id' => ['required', 'exists:vendor,id'],
            'tanggal' => ['required', 'date'],
            'tipe_pembayaran' => ['required', Rule::in([Pembelian::TIPE_TUNAI, Pembelian::TIPE_KREDIT])],
            'dibayar' => ['required', 'numeric', 'gte:0'],
            'jatuh_tempo' => ['nullable', 'required_if:tipe_pembayaran,kredit', 'date', 'after_or_equal:tanggal'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'distinct', 'exists:barang,id'],
            'detail.*.jumlah' => ['required', 'integer', 'gte:1'],
            'detail.*.harga_beli' => ['required', 'numeric', 'gt:0'],
        ];
    }
}

<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\BaseFormRequest;
use App\Models\Penjualan;
use Illuminate\Validation\Rule;

class StorePenjualanRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'nomor_penjualan' => ['required', 'string', 'max:100', Rule::unique('penjualan', 'nomor_penjualan')],
            'pelanggan_id' => ['nullable', 'required_if:tipe_pembayaran,kredit', 'exists:pelanggan,id'],
            'tanggal' => ['required', 'date'],
            'tipe_pembayaran' => ['required', Rule::in([Penjualan::TIPE_TUNAI, Penjualan::TIPE_KREDIT])],
            'dibayar' => ['required', 'numeric', 'gte:0'],
            'jatuh_tempo' => ['nullable', 'required_if:tipe_pembayaran,kredit', 'date', 'after_or_equal:tanggal'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'distinct', 'exists:barang,id'],
            'detail.*.jumlah' => ['required', 'integer', 'gte:1'],
            'detail.*.harga_jual' => ['required', 'numeric', 'gt:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nomor_penjualan' => 'nomor penjualan',
            'pelanggan_id' => 'pelanggan',
            'tanggal' => 'tanggal penjualan',
            'tipe_pembayaran' => 'tipe pembayaran',
            'dibayar' => 'jumlah dibayar',
            'jatuh_tempo' => 'jatuh tempo',
            'catatan' => 'catatan',
            'detail' => 'detail penjualan',
            'detail.*.barang_id' => 'barang',
            'detail.*.jumlah' => 'jumlah',
            'detail.*.harga_jual' => 'harga jual',
        ];
    }
}

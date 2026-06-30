<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\BaseFormRequest;
use App\Models\PembayaranUtang;
use App\Models\Pembelian;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePembayaranUtangRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'pembelian_id' => [
                'required',
                Rule::exists('pembelian', 'id')->where(
                    fn ($query) => $query->where('tipe_pembayaran', Pembelian::TIPE_KREDIT)
                ),
            ],
            'tanggal' => ['required', 'date'],
            'jumlah_bayar' => ['required', 'numeric', 'gt:0'],
            'metode_pembayaran' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $pembelian = Pembelian::find($this->input('pembelian_id'));

            if (! $pembelian) {
                return;
            }

            $pembayaranId = $this->routeModelId(['pembayaran_utang', 'pembayaranUtang']);
            $pembayaran = $pembayaranId ? PembayaranUtang::find($pembayaranId) : null;

            $maksimalPembayaran = (float) $pembelian->sisa_utang;

            if ($pembayaran && (int) $pembayaran->pembelian_id === (int) $pembelian->id) {
                $maksimalPembayaran += (float) $pembayaran->jumlah_bayar;
            }

            if ((float) $this->input('jumlah_bayar', 0) > $maksimalPembayaran) {
                $validator->errors()->add('jumlah_bayar', 'Jumlah bayar melebihi sisa utang.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'pembelian_id' => 'transaksi stok masuk',
            'tanggal' => 'tanggal pembayaran',
            'jumlah_bayar' => 'jumlah bayar',
            'metode_pembayaran' => 'metode pembayaran',
            'catatan' => 'catatan',
        ];
    }
}

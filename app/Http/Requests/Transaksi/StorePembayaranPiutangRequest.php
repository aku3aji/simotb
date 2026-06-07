<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\BaseFormRequest;
use App\Models\PembayaranPiutang;
use App\Models\Penjualan;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePembayaranPiutangRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'penjualan_id' => [
                'required',
                Rule::exists('penjualan', 'id')->where(
                    fn ($query) => $query->where('tipe_pembayaran', Penjualan::TIPE_KREDIT)
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
            $penjualan = Penjualan::find($this->input('penjualan_id'));

            if (! $penjualan) {
                return;
            }

            $pembayaranId = $this->routeModelId(['pembayaran_piutang', 'pembayaranPiutang']);
            $pembayaran = $pembayaranId ? PembayaranPiutang::find($pembayaranId) : null;

            $maksimalPembayaran = (float) $penjualan->sisa_piutang;

            if ($pembayaran && (int) $pembayaran->penjualan_id === (int) $penjualan->id) {
                $maksimalPembayaran += (float) $pembayaran->jumlah_bayar;
            }

            if ((float) $this->input('jumlah_bayar', 0) > $maksimalPembayaran) {
                $validator->errors()->add('jumlah_bayar', 'Jumlah bayar melebihi sisa piutang.');
            }
        });
    }

    public function attributes(): array
    {
        return [
            'penjualan_id' => 'transaksi penjualan',
            'tanggal' => 'tanggal pembayaran',
            'jumlah_bayar' => 'jumlah bayar',
            'metode_pembayaran' => 'metode pembayaran',
            'catatan' => 'catatan',
        ];
    }
}

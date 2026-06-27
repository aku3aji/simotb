<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\BaseFormRequest;
use App\Models\ReturPenjualanDetail as DetailRetur;
use App\Models\Penjualan;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreReturPenjualanRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'penjualan_id' => ['required', 'exists:penjualan,id'],
            'tanggal' => ['required', 'date'],
            'alasan' => ['nullable', 'string', 'max:1000'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.barang_id' => ['required', 'distinct', 'exists:barang,id'],
            'detail.*.jumlah' => ['required', 'integer', 'gte:1'],
            'detail.*.harga_jual' => ['required', 'numeric', 'gte:0'],
            'detail.*.kondisi_barang' => [
                'required',
                Rule::in([DetailRetur::KONDISI_BAIK, DetailRetur::KONDISI_RUSAK]),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $penjualan = Penjualan::with('detail')->find($this->input('penjualan_id'));

            if (! $penjualan) {
                return;
            }

            if ($this->filled('tanggal') && $this->input('tanggal') < $penjualan->tanggal?->format('Y-m-d')) {
                $validator->errors()->add('tanggal', 'Tanggal retur tidak boleh sebelum tanggal penjualan.');
            }

            $returId = $this->routeModelId(['retur_penjualan', 'returPenjualan']);

            $jumlahTerjual = $penjualan->detail->pluck('jumlah', 'barang_id');

            $jumlahReturSebelumnya = DetailRetur::query()
                ->select('retur_penjualan_detail.barang_id')
                ->selectRaw('SUM(retur_penjualan_detail.jumlah) as total_retur')
                ->join('retur_penjualan', 'retur_penjualan.id', '=', 'retur_penjualan_detail.retur_penjualan_id')
                ->where('retur_penjualan.penjualan_id', $penjualan->id)
                ->when($returId, fn ($query) => $query->where('retur_penjualan.id', '!=', $returId))
                ->groupBy('retur_penjualan_detail.barang_id')
                ->pluck('total_retur', 'retur_penjualan_detail.barang_id');

            foreach ($this->input('detail', []) as $index => $item) {
                $barangId = $item['barang_id'] ?? null;
                $jumlah = (int) ($item['jumlah'] ?? 0);

                if (! $barangId || ! isset($jumlahTerjual[$barangId])) {
                    $validator->errors()->add("detail.$index.barang_id", 'Barang retur harus berasal dari transaksi penjualan yang dipilih.');
                    continue;
                }

                $maksimalRetur = (int) $jumlahTerjual[$barangId] - (int) ($jumlahReturSebelumnya[$barangId] ?? 0);

                if ($jumlah > $maksimalRetur) {
                    $validator->errors()->add("detail.$index.jumlah", 'Jumlah retur melebihi jumlah barang yang dapat diretur.');
                }
            }
        });
    }

    public function attributes(): array
    {
        return [
            'nomor_retur' => 'nomor retur',
            'penjualan_id' => 'transaksi penjualan',
            'tanggal' => 'tanggal retur',
            'alasan' => 'alasan retur',
            'detail' => 'detail retur',
            'detail.*.barang_id' => 'barang',
            'detail.*.jumlah' => 'jumlah retur',
            'detail.*.harga_jual' => 'harga jual',
            'detail.*.kondisi_barang' => 'kondisi barang',
        ];
    }
}

<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\BaseFormRequest;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use Illuminate\Validation\Rule;

class StorePenjualanRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        // Create pelanggan baru inline jika dipilih opsi tambah baru
        if ($this->input('pelanggan_id') === '__new__') {
            $nama = trim($this->input('pelanggan_nama_baru', ''));
            if ($nama !== '') {
                $pelanggan = Pelanggan::create(['nama' => $nama]);
                $this->merge(['pelanggan_id' => (string) $pelanggan->id]);
            } else {
                $this->merge(['pelanggan_id' => null]);
            }
        }

        // Create barang baru inline untuk setiap baris detail yang menggunakan opsi tambah baru
        $detail = $this->input('detail', []);
        $changed = false;
        $newlyCreatedIds = [];
        foreach ($detail as $i => $item) {
            if (($item['barang_id'] ?? '') === '__new__') {
                $changed = true;
                $nama = trim($item['barang_nama_baru'] ?? '');
                if ($nama !== '') {
                    $barang = Barang::create([
                        'kode_barang' => $this->generateKodeBarang($nama),
                        'nama'        => $nama,
                        'harga_jual'  => (float) ($item['harga_jual'] ?? 0),
                        'harga_beli'  => 0,
                        'stok'        => 0,
                        'stok_minimum' => 0,
                        'is_active'   => true,
                    ]);
                    $detail[$i]['barang_id'] = (string) $barang->id;
                    $newlyCreatedIds[] = $barang->id;
                } else {
                    $detail[$i]['barang_id'] = '';
                }
            }
        }
        if ($changed) {
            $this->merge(['detail' => $detail]);
        }
        if (!empty($newlyCreatedIds)) {
            $this->merge(['newly_created_barang_ids' => $newlyCreatedIds]);
        }
    }

    public function rules(): array
    {
        return [
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
            'newly_created_barang_ids' => ['nullable', 'array'],
            'newly_created_barang_ids.*' => ['integer'],
        ];
    }

    public function attributes(): array
    {
        return [
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

    private function generateKodeBarang(string $nama): string
    {
        $slug = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $nama), 0, 4));
        $prefix = 'BRG-' . ($slug ?: 'XX') . '-';
        $count = Barang::where('kode_barang', 'like', $prefix . '%')->count();
        $kode = $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        while (Barang::where('kode_barang', $kode)->exists()) {
            $count++;
            $kode = $prefix . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        }
        return $kode;
    }
}

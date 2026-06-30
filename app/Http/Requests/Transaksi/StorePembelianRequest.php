<?php

namespace App\Http\Requests\Transaksi;

use App\Http\Requests\BaseFormRequest;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Vendor;
use Illuminate\Validation\Rule;

class StorePembelianRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        // Create vendor baru inline jika dipilih opsi tambah baru
        if ($this->input('vendor_id') === '__new__') {
            $nama = trim($this->input('vendor_nama_baru', ''));
            if ($nama !== '') {
                $vendor = Vendor::create(['nama' => $nama]);
                $this->merge(['vendor_id' => (string) $vendor->id]);
            } else {
                $this->merge(['vendor_id' => '']);
            }
        }

        // Create barang baru inline untuk setiap baris detail yang menggunakan opsi tambah baru
        $detail = $this->input('detail', []);
        $changed = false;
        foreach ($detail as $i => $item) {
            if (($item['barang_id'] ?? '') === '__new__') {
                $changed = true;
                $nama = trim($item['barang_nama_baru'] ?? '');
                if ($nama !== '') {
                    $barang = Barang::create([
                        'kode_barang'  => $this->generateKodeBarang($nama),
                        'nama'         => $nama,
                        'harga_beli'   => (float) ($item['harga_beli'] ?? 0),
                        'harga_jual'   => 0,
                        'stok'         => 0,
                        'stok_minimum' => 0,
                        'is_active'    => true,
                    ]);
                    $detail[$i]['barang_id'] = (string) $barang->id;
                } else {
                    $detail[$i]['barang_id'] = '';
                }
            }
        }
        if ($changed) {
            $this->merge(['detail' => $detail]);
        }
    }

    public function rules(): array
    {
        return [
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

    public function attributes(): array
    {
        return [
            'vendor_id' => 'vendor',
            'tanggal' => 'tanggal pembelian',
            'tipe_pembayaran' => 'tipe pembayaran',
            'dibayar' => 'jumlah dibayar',
            'jatuh_tempo' => 'jatuh tempo',
            'catatan' => 'catatan',
            'detail' => 'detail pembelian',
            'detail.*.barang_id' => 'barang',
            'detail.*.jumlah' => 'jumlah',
            'detail.*.harga_beli' => 'harga beli',
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

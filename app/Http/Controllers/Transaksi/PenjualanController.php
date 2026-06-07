<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Concerns\InteractsWithStok;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePenjualanRequest;
use App\Http\Requests\Transaksi\UpdatePenjualanRequest;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\StokMutasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    use InteractsWithStok;

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $tipePembayaran = $request->string('tipe_pembayaran')->trim()->toString();
        $statusPembayaran = $request->string('status_pembayaran')->trim()->toString();
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();

        $penjualan = Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nomor_penjualan', 'like', '%' . $q . '%')
                        ->orWhereHas('pelanggan', fn ($pelanggan) => $pelanggan->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tipePembayaran !== '', fn ($query) => $query->where('tipe_pembayaran', $tipePembayaran))
            ->when($statusPembayaran !== '', fn ($query) => $query->where('status_pembayaran', $statusPembayaran))
            ->when($tanggalMulai !== '', fn ($query) => $query->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->whereDate('tanggal', '<=', $tanggalSelesai))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('transaksi.penjualan.index', compact(
            'penjualan',
            'q',
            'tipePembayaran',
            'statusPembayaran',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function create(): View
    {
        return view('transaksi.penjualan.create', $this->formData());
    }

    public function store(StorePenjualanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $detailRows = $this->prepareDetailRows($validated['detail']);
        $total = (float) $detailRows->sum('subtotal');
        $ringkasanPembayaran = $this->resolvePaymentSummary(
            $validated['tipe_pembayaran'],
            $total,
            (float) $validated['dibayar'],
            $validated['jatuh_tempo'] ?? null
        );
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $detailRows, $total, $ringkasanPembayaran, $userId) {
            $barangMap = Barang::query()
                ->whereIn('id', $detailRows->pluck('barang_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($detailRows as $item) {
                $barang = $barangMap->get($item['barang_id']);

                if ((int) $barang->stok < (int) $item['jumlah']) {
                    throw ValidationException::withMessages([
                        'detail' => ['Stok barang ' . $barang->nama . ' tidak mencukupi.'],
                    ]);
                }
            }

            $penjualan = Penjualan::create([
                'nomor_penjualan' => $validated['nomor_penjualan'],
                'pelanggan_id' => $validated['pelanggan_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'tipe_pembayaran' => $validated['tipe_pembayaran'],
                'total' => $total,
                'dibayar' => $ringkasanPembayaran['dibayar'],
                'sisa_piutang' => $ringkasanPembayaran['sisa_piutang'],
                'status_pembayaran' => $ringkasanPembayaran['status_pembayaran'],
                'jatuh_tempo' => $ringkasanPembayaran['jatuh_tempo'],
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => $userId,
            ]);

            foreach ($detailRows as $item) {
                $barang = $barangMap->get($item['barang_id']);
                $stokSebelum = (int) $barang->stok;
                $stokSesudah = $stokSebelum - (int) $item['jumlah'];

                $penjualan->detail()->create($item);

                $barang->update([
                    'stok' => $stokSesudah,
                ]);

                $this->catatMutasiStok(
                    $barang,
                    StokMutasi::TIPE_KELUAR,
                    StokMutasi::SUMBER_PENJUALAN,
                    $penjualan->id,
                    (int) $item['jumlah'],
                    $stokSebelum,
                    $stokSesudah,
                    $userId,
                    'Pengurangan stok dari transaksi penjualan.'
                );
            }
        });

        return redirect()->route('transaksi.penjualan.index')
            ->with('success', 'Transaksi penjualan berhasil disimpan.');
    }

    public function edit(Penjualan $penjualan): View
    {
        $penjualan->load('detail');

        return view('transaksi.penjualan.edit', array_merge(
            $this->formData(),
            compact('penjualan')
        ));
    }

    public function update(UpdatePenjualanRequest $request, Penjualan $penjualan): RedirectResponse
    {
        $validated = $request->validated();
        $detailRows = $this->prepareDetailRows($validated['detail']);
        $newDetails = $detailRows->keyBy('barang_id');
        $ringkasanPembayaran = $this->resolvePaymentSummary(
            $validated['tipe_pembayaran'],
            (float) $detailRows->sum('subtotal'),
            (float) $validated['dibayar'],
            $validated['jatuh_tempo'] ?? null
        );
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $detailRows, $newDetails, $ringkasanPembayaran, $penjualan, $userId) {
            $penjualan = Penjualan::query()->lockForUpdate()->findOrFail($penjualan->id);

            if ($penjualan->pembayaranPiutang()->exists() || $penjualan->returPenjualan()->exists()) {
                throw ValidationException::withMessages([
                    'error' => ['Penjualan yang sudah memiliki pembayaran piutang atau retur tidak dapat diubah.'],
                ]);
            }

            $existingDetails = $penjualan->detail()->get()->keyBy('barang_id');
            $affectedBarangIds = $existingDetails->keys()->merge($newDetails->keys())->unique()->values();

            $barangMap = Barang::query()
                ->whereIn('id', $affectedBarangIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($affectedBarangIds as $barangId) {
                $barang = $barangMap->get($barangId);
                $oldQty = (int) data_get($existingDetails->get($barangId), 'jumlah', 0);
                $newQty = (int) data_get($newDetails->get($barangId), 'jumlah', 0);
                $stokBaru = (int) $barang->stok - ($newQty - $oldQty);

                if ($stokBaru < 0) {
                    throw ValidationException::withMessages([
                        'detail' => ['Perubahan penjualan membuat stok barang ' . $barang->nama . ' tidak mencukupi.'],
                    ]);
                }
            }

            $penjualan->update([
                'nomor_penjualan' => $validated['nomor_penjualan'],
                'pelanggan_id' => $validated['pelanggan_id'] ?? null,
                'tanggal' => $validated['tanggal'],
                'tipe_pembayaran' => $validated['tipe_pembayaran'],
                'total' => (float) $detailRows->sum('subtotal'),
                'dibayar' => $ringkasanPembayaran['dibayar'],
                'sisa_piutang' => $ringkasanPembayaran['sisa_piutang'],
                'status_pembayaran' => $ringkasanPembayaran['status_pembayaran'],
                'jatuh_tempo' => $ringkasanPembayaran['jatuh_tempo'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $penjualan->detail()->delete();

            foreach ($detailRows as $item) {
                $penjualan->detail()->create($item);
            }

            foreach ($affectedBarangIds as $barangId) {
                $barang = $barangMap->get($barangId);
                $oldQty = (int) data_get($existingDetails->get($barangId), 'jumlah', 0);
                $newQty = (int) data_get($newDetails->get($barangId), 'jumlah', 0);
                $delta = $newQty - $oldQty;
                $stokSebelum = (int) $barang->stok;
                $stokSesudah = $stokSebelum - $delta;

                $barang->stok = $stokSesudah;
                $barang->save();

                $this->catatMutasiStok(
                    $barang,
                    $delta >= 0 ? StokMutasi::TIPE_KELUAR : StokMutasi::TIPE_MASUK,
                    StokMutasi::SUMBER_PENJUALAN,
                    $penjualan->id,
                    abs($delta),
                    $stokSebelum,
                    $stokSesudah,
                    $userId,
                    'Penyesuaian stok dari perubahan transaksi penjualan.'
                );
            }
        });

        return redirect()->route('transaksi.penjualan.index')
            ->with('success', 'Transaksi penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan): RedirectResponse
    {
        return redirect()->route('transaksi.penjualan.index')
            ->with('error', 'Transaksi penjualan yang sudah tersimpan tidak dapat dihapus.');
    }

    private function formData(): array
    {
        return [
            'pelangganList' => Pelanggan::query()->orderBy('nama')->get(),
            'barangList' => Barang::query()
                ->with(['kategori', 'satuan', 'merek'])
                ->aktif()
                ->orderBy('nama')
                ->get(),
        ];
    }

    private function prepareDetailRows(array $detail): Collection
    {
        return collect($detail)->map(function ($item) {
            $jumlah = (int) $item['jumlah'];
            $hargaJual = (float) $item['harga_jual'];

            return [
                'barang_id' => (int) $item['barang_id'],
                'jumlah' => $jumlah,
                'harga_jual' => $hargaJual,
                'subtotal' => $jumlah * $hargaJual,
            ];
        });
    }

    private function resolvePaymentSummary(
        string $tipePembayaran,
        float $total,
        float $dibayar,
        ?string $jatuhTempo = null
    ): array
    {
        if ($tipePembayaran === Penjualan::TIPE_TUNAI) {
            return [
                'dibayar' => $total,
                'sisa_piutang' => 0,
                'status_pembayaran' => Penjualan::STATUS_LUNAS,
                'jatuh_tempo' => null,
            ];
        }

        if ($dibayar > $total) {
            throw ValidationException::withMessages([
                'dibayar' => ['Jumlah dibayar tidak boleh melebihi total penjualan.'],
            ]);
        }

        $sisaPiutang = $total - $dibayar;

        return [
            'dibayar' => $dibayar,
            'sisa_piutang' => $sisaPiutang,
            'status_pembayaran' => $dibayar <= 0
                ? Penjualan::STATUS_BELUM_LUNAS
                : ($sisaPiutang > 0 ? Penjualan::STATUS_SEBAGIAN : Penjualan::STATUS_LUNAS),
            'jatuh_tempo' => $jatuhTempo,
        ];
    }
}

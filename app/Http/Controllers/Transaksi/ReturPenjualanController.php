<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Concerns\InteractsWithStok;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StoreReturPenjualanRequest;
use App\Http\Requests\Transaksi\UpdateReturPenjualanRequest;
use App\Models\Barang;
use App\Models\Penjualan;
use App\Models\ReturPenjualan;
use App\Models\ReturPenjualanDetail;
use App\Models\StokMutasi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReturPenjualanController extends Controller
{
    use InteractsWithStok;

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();

        $returPenjualan = ReturPenjualan::query()
            ->with(['penjualan.pelanggan', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nomor_retur', 'like', '%' . $q . '%')
                        ->orWhereHas('penjualan', fn ($penjualan) => $penjualan->where('nomor_penjualan', 'like', '%' . $q . '%'))
                        ->orWhereHas('penjualan.pelanggan', fn ($pelanggan) => $pelanggan->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tanggalMulai !== '', fn ($query) => $query->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->whereDate('tanggal', '<=', $tanggalSelesai))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('transaksi.retur-penjualan.index', compact(
            'returPenjualan',
            'q',
            'tanggalMulai',
            'tanggalSelesai'
        ));
    }

    public function create(): View
    {
        return view('transaksi.retur-penjualan.create', [
            'penjualanList' => $this->penjualanList(),
        ]);
    }

    public function store(StoreReturPenjualanRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $detailRows = $this->prepareDetailRows($validated['detail']);
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $detailRows, $userId) {
            $returPenjualan = ReturPenjualan::create([
                'nomor_retur' => $validated['nomor_retur'],
                'penjualan_id' => $validated['penjualan_id'],
                'tanggal' => $validated['tanggal'],
                'total_retur' => (float) $detailRows->sum('subtotal'),
                'alasan' => $validated['alasan'] ?? null,
                'user_id' => $userId,
            ]);

            $barangIds = $detailRows
                ->where('kondisi_barang', ReturPenjualanDetail::KONDISI_BAIK)
                ->pluck('barang_id')
                ->unique()
                ->values();

            $barangMap = $barangIds->isNotEmpty()
                ? Barang::query()->whereIn('id', $barangIds)->lockForUpdate()->get()->keyBy('id')
                : collect();

            foreach ($detailRows as $item) {
                $returPenjualan->detail()->create($item);

                if ($item['kondisi_barang'] !== ReturPenjualanDetail::KONDISI_BAIK) {
                    continue;
                }

                $barang = $barangMap->get($item['barang_id']);
                $stokSebelum = (int) $barang->stok;
                $stokSesudah = $stokSebelum + (int) $item['jumlah'];

                $barang->update([
                    'stok' => $stokSesudah,
                ]);

                $this->catatMutasiStok(
                    $barang,
                    StokMutasi::TIPE_MASUK,
                    StokMutasi::SUMBER_RETUR_PENJUALAN,
                    $returPenjualan->id,
                    (int) $item['jumlah'],
                    $stokSebelum,
                    $stokSesudah,
                    $userId,
                    'Penambahan stok dari retur penjualan.'
                );
            }
        });

        return redirect()->route('transaksi.retur-penjualan.index')
            ->with('success', 'Retur penjualan berhasil disimpan.');
    }

    public function edit(ReturPenjualan $returPenjualan): View
    {
        $returPenjualan->load('detail');

        return view('transaksi.retur-penjualan.edit', [
            'returPenjualan' => $returPenjualan,
            'penjualanList' => $this->penjualanList($returPenjualan->penjualan_id),
        ]);
    }

    public function update(UpdateReturPenjualanRequest $request, ReturPenjualan $returPenjualan): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['penjualan_id'] !== (int) $returPenjualan->penjualan_id) {
            return back()->withInput()
                ->with('error', 'Retur tidak dapat dipindahkan ke transaksi penjualan lain.');
        }

        $detailRows = $this->prepareDetailRows($validated['detail']);
        $newDetails = $detailRows->keyBy('barang_id');
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $detailRows, $newDetails, $returPenjualan, $userId) {
            $returPenjualan = ReturPenjualan::query()->lockForUpdate()->findOrFail($returPenjualan->id);

            $existingDetails = $returPenjualan->detail()->get()->keyBy('barang_id');
            $affectedBarangIds = $existingDetails->keys()->merge($newDetails->keys())->unique()->values();

            $barangMap = Barang::query()
                ->whereIn('id', $affectedBarangIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($affectedBarangIds as $barangId) {
                $barang = $barangMap->get($barangId);
                $oldStockEffect = $this->stockEffect($existingDetails->get($barangId));
                $newStockEffect = $this->stockEffect($newDetails->get($barangId));
                $stokBaru = (int) $barang->stok + ($newStockEffect - $oldStockEffect);

                if ($stokBaru < 0) {
                    throw ValidationException::withMessages([
                        'detail' => ['Perubahan retur membuat stok barang ' . $barang->nama . ' menjadi minus.'],
                    ]);
                }
            }

            $returPenjualan->update([
                'nomor_retur' => $validated['nomor_retur'],
                'penjualan_id' => $validated['penjualan_id'],
                'tanggal' => $validated['tanggal'],
                'total_retur' => (float) $detailRows->sum('subtotal'),
                'alasan' => $validated['alasan'] ?? null,
            ]);

            $returPenjualan->detail()->delete();

            foreach ($detailRows as $item) {
                $returPenjualan->detail()->create($item);
            }

            foreach ($affectedBarangIds as $barangId) {
                $barang = $barangMap->get($barangId);
                $oldStockEffect = $this->stockEffect($existingDetails->get($barangId));
                $newStockEffect = $this->stockEffect($newDetails->get($barangId));
                $delta = $newStockEffect - $oldStockEffect;
                $stokSebelum = (int) $barang->stok;
                $stokSesudah = $stokSebelum + $delta;

                $barang->stok = $stokSesudah;
                $barang->save();

                $this->catatMutasiStok(
                    $barang,
                    $delta >= 0 ? StokMutasi::TIPE_MASUK : StokMutasi::TIPE_KELUAR,
                    StokMutasi::SUMBER_RETUR_PENJUALAN,
                    $returPenjualan->id,
                    abs($delta),
                    $stokSebelum,
                    $stokSesudah,
                    $userId,
                    'Penyesuaian stok dari perubahan retur penjualan.'
                );
            }
        });

        return redirect()->route('transaksi.retur-penjualan.index')
            ->with('success', 'Retur penjualan berhasil diperbarui.');
    }

    public function destroy(ReturPenjualan $returPenjualan): RedirectResponse
    {
        return redirect()->route('transaksi.retur-penjualan.index')
            ->with('error', 'Retur penjualan yang sudah tersimpan tidak dapat dihapus.');
    }

    private function penjualanList(?int $currentPenjualanId = null)
    {
        return Penjualan::query()
            ->with(['pelanggan', 'detail.barang'])
            ->where(function ($query) use ($currentPenjualanId) {
                $query->whereHas('detail');

                if ($currentPenjualanId) {
                    $query->orWhere('id', $currentPenjualanId);
                }
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
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
                'kondisi_barang' => $item['kondisi_barang'],
            ];
        });
    }

    private function stockEffect(mixed $detail): int
    {
        if (! $detail) {
            return 0;
        }

        if (data_get($detail, 'kondisi_barang') !== ReturPenjualanDetail::KONDISI_BAIK) {
            return 0;
        }

        return (int) data_get($detail, 'jumlah', 0);
    }
}

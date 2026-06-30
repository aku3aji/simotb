<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Concerns\InteractsWithStok;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePembelianRequest;
use App\Http\Requests\Transaksi\UpdatePembelianRequest;
use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\StokMutasi;
use App\Models\Vendor;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembelianController extends Controller
{
    use InteractsWithStok;

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $vendorId = $request->string('vendor_id')->trim()->toString();
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();

        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['nomor_pembelian', 'tanggal'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }

        $statHariIni = Pembelian::query()
            ->where('tanggal', now()->toDateString())
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        $statBulanIni = Pembelian::query()
            ->whereBetween('tanggal', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        $pembelian = Pembelian::query()
            ->with(['vendor', 'user'])
            ->withCount('pembayaranUtang')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nomor_pembelian', 'like', '%' . $q . '%')
                        ->orWhereHas('vendor', fn ($vendor) => $vendor->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($vendorId !== '', fn ($query) => $query->where('vendor_id', $vendorId))
            ->when($tanggalMulai !== '', fn ($query) => $query->where('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->where('tanggal', '<=', $tanggalSelesai))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('transaksi.pembelian.index', array_merge(
            compact('pembelian', 'q', 'vendorId', 'tanggalMulai', 'tanggalSelesai',
                    'sortBy', 'sortDir', 'perPage', 'statHariIni', 'statBulanIni'),
            [
                'vendorList' => Vendor::query()->orderBy('nama')->get(),
            ]
        ));
    }

    public function create(): View
    {
        return view('transaksi.pembelian.create', array_merge(
            $this->formData(),
            ['nomorPembelian' => $this->generateNomor()]
        ));
    }

    public function store(StorePembelianRequest $request): RedirectResponse
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
            $pembelian = Pembelian::create([
                'nomor_pembelian' => $this->generateNomor(),
                'vendor_id' => $validated['vendor_id'],
                'tanggal' => $validated['tanggal'],
                'tipe_pembayaran' => $validated['tipe_pembayaran'],
                'total' => $total,
                'dibayar' => $ringkasanPembayaran['dibayar'],
                'sisa_utang' => $ringkasanPembayaran['sisa_utang'],
                'status_pembayaran' => $ringkasanPembayaran['status_pembayaran'],
                'jatuh_tempo' => $ringkasanPembayaran['jatuh_tempo'],
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => $userId,
            ]);

            $barangMap = Barang::query()
                ->whereIn('id', $detailRows->pluck('barang_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($detailRows as $item) {
                $barang = $barangMap->get($item['barang_id']);
                $stokSebelum = (int) $barang->stok;
                $stokSesudah = $stokSebelum + (int) $item['jumlah'];

                try {
                    $pembelian->detail()->create($item);
                } catch (QueryException $e) {
                    throw ValidationException::withMessages([
                        'detail' => ['Terdapat barang yang sama dalam satu transaksi pembelian.'],
                    ]);
                }

                $barang->update([
                    'stok' => $stokSesudah,
                    'harga_beli' => $item['harga_beli'],
                ]);

                $this->catatMutasiStok(
                    $barang,
                    StokMutasi::TIPE_MASUK,
                    StokMutasi::SUMBER_PEMBELIAN,
                    $pembelian->id,
                    (int) $item['jumlah'],
                    $stokSebelum,
                    $stokSesudah,
                    $userId,
                    'Penambahan stok dari transaksi pembelian.'
                );
            }
        });

        return redirect()->route('transaksi.stok-masuk.index')
            ->with('success', 'Transaksi pembelian berhasil disimpan.');
    }

    public function show(Pembelian $pembelian): View
    {
        $pembelian->load([
            'detail.barang',
            'vendor',
            'user',
            'pembayaranUtang.user',
        ]);

        return view('transaksi.pembelian.show', compact('pembelian'));
    }

    public function edit(Pembelian $pembelian): View
    {
        $pembelian->load('detail');

        return view('transaksi.pembelian.edit', array_merge(
            $this->formData(),
            compact('pembelian')
        ));
    }

    public function update(UpdatePembelianRequest $request, Pembelian $pembelian): RedirectResponse
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

        DB::transaction(function () use ($validated, $detailRows, $newDetails, $ringkasanPembayaran, $pembelian, $userId) {
            $pembelian = Pembelian::query()->lockForUpdate()->findOrFail($pembelian->id);

            if ($pembelian->pembayaranUtang()->exists()) {
                throw ValidationException::withMessages([
                    'error' => ['Stok masuk yang sudah memiliki pembayaran utang tidak dapat diubah.'],
                ]);
            }

            $existingDetails = $pembelian->detail()->get()->keyBy('barang_id');
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
                $stokBaru = (int) $barang->stok + ($newQty - $oldQty);

                if ($stokBaru < 0) {
                    throw ValidationException::withMessages([
                        'detail' => ['Perubahan pembelian membuat stok barang ' . $barang->nama . ' menjadi minus.'],
                    ]);
                }
            }

            $pembelian->update([
                'nomor_pembelian' => $validated['nomor_pembelian'],
                'vendor_id' => $validated['vendor_id'],
                'tanggal' => $validated['tanggal'],
                'tipe_pembayaran' => $validated['tipe_pembayaran'],
                'total' => (float) $detailRows->sum('subtotal'),
                'dibayar' => $ringkasanPembayaran['dibayar'],
                'sisa_utang' => $ringkasanPembayaran['sisa_utang'],
                'status_pembayaran' => $ringkasanPembayaran['status_pembayaran'],
                'jatuh_tempo' => $ringkasanPembayaran['jatuh_tempo'],
                'catatan' => $validated['catatan'] ?? null,
            ]);

            $pembelian->detail()->delete();

            foreach ($detailRows as $item) {
                $pembelian->detail()->create($item);
            }

            foreach ($affectedBarangIds as $barangId) {
                $barang = $barangMap->get($barangId);
                $oldQty = (int) data_get($existingDetails->get($barangId), 'jumlah', 0);
                $newDetail = $newDetails->get($barangId);
                $newQty = (int) data_get($newDetail, 'jumlah', 0);
                $delta = $newQty - $oldQty;
                $stokSebelum = (int) $barang->stok;
                $stokSesudah = $stokSebelum + $delta;

                if ($newDetail) {
                    $barang->harga_beli = $newDetail['harga_beli'];
                }

                $barang->stok = $stokSesudah;
                $barang->save();

                if ($delta !== 0) {
                    $this->catatMutasiStok(
                        $barang,
                        $delta >= 0 ? StokMutasi::TIPE_MASUK : StokMutasi::TIPE_KELUAR,
                        StokMutasi::SUMBER_PEMBELIAN,
                        $pembelian->id,
                        abs($delta),
                        $stokSebelum,
                        $stokSesudah,
                        $userId,
                        'Penyesuaian stok dari perubahan transaksi pembelian.'
                    );
                }
            }
        });

        return redirect()->route('transaksi.stok-masuk.index')
            ->with('success', 'Transaksi pembelian berhasil diperbarui.');
    }

    public function destroy(Pembelian $pembelian): RedirectResponse
    {
        return redirect()->route('transaksi.stok-masuk.index')
            ->with('error', 'Transaksi pembelian yang sudah tersimpan tidak dapat dihapus.');
    }

    private function generateNomor(): string
    {
        $prefix = 'PBL-' . now()->format('Ymd') . '-';
        $last = Pembelian::query()
            ->where('nomor_pembelian', 'like', $prefix . '%')
            ->orderByDesc('nomor_pembelian')
            ->lockForUpdate()
            ->value('nomor_pembelian');
        $next = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;
        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function formData(): array
    {
        return [
            'vendorList' => Vendor::query()->orderBy('nama')->get(),
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
            $hargaBeli = (float) $item['harga_beli'];

            return [
                'barang_id' => (int) $item['barang_id'],
                'jumlah' => $jumlah,
                'harga_beli' => $hargaBeli,
                'subtotal' => $jumlah * $hargaBeli,
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
        if ($tipePembayaran === Pembelian::TIPE_TUNAI) {
            return [
                'dibayar' => $total,
                'sisa_utang' => 0,
                'status_pembayaran' => Pembelian::STATUS_LUNAS,
                'jatuh_tempo' => null,
            ];
        }

        if ($dibayar > $total) {
            throw ValidationException::withMessages([
                'dibayar' => ['Jumlah dibayar tidak boleh melebihi total stok masuk.'],
            ]);
        }

        $sisaUtang = $total - $dibayar;

        return [
            'dibayar' => $dibayar,
            'sisa_utang' => $sisaUtang,
            'status_pembayaran' => $dibayar <= 0
                ? Pembelian::STATUS_BELUM_LUNAS
                : ($sisaUtang > 0 ? Pembelian::STATUS_SEBAGIAN : Pembelian::STATUS_LUNAS),
            'jatuh_tempo' => $jatuhTempo,
        ];
    }
}

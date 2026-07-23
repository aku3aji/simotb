<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Concerns\InteractsWithStok;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePenjualanRequest;
use App\Http\Requests\Transaksi\UpdatePenjualanRequest;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pengaturan;
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

        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['nomor_penjualan', 'tanggal', 'tipe_pembayaran', 'status_pembayaran'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }

        $statHariIni = Penjualan::query()
            ->where('tanggal', now()->toDateString())
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        $statBulanIni = Penjualan::query()
            ->whereBetween('tanggal', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as total')
            ->first();

        $penjualan = Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->withCount('returPenjualan')
            ->withSum('returPenjualan', 'total_retur')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('nomor_penjualan', 'like', '%' . $q . '%')
                        ->orWhereHas('pelanggan', fn ($pelanggan) => $pelanggan->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tipePembayaran !== '', fn ($query) => $query->where('tipe_pembayaran', $tipePembayaran))
            ->when($statusPembayaran !== '', fn ($query) => $query->where('status_pembayaran', $statusPembayaran))
            ->when($tanggalMulai !== '', fn ($query) => $query->where('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->where('tanggal', '<=', $tanggalSelesai))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('transaksi.penjualan.index', compact(
            'penjualan',
            'q',
            'tipePembayaran',
            'statusPembayaran',
            'tanggalMulai',
            'tanggalSelesai',
            'sortBy',
            'sortDir',
            'perPage',
            'statHariIni',
            'statBulanIni'
        ));
    }

    public function create(): View
    {
        return view('transaksi.penjualan.create', array_merge(
            $this->formData(),
            ['nomorPenjualan' => $this->generateNomor()]
        ));
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

        $newlyCreatedIds = collect($validated['newly_created_barang_ids'] ?? []);

        DB::transaction(function () use ($validated, $detailRows, $total, $ringkasanPembayaran, $userId, $newlyCreatedIds) {
            $barangMap = Barang::query()
                ->whereIn('id', $detailRows->pluck('barang_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($detailRows as $item) {
                $barang = $barangMap->get($item['barang_id']);
                if (!$newlyCreatedIds->contains($item['barang_id']) && (int) $barang->stok < (int) $item['jumlah']) {
                    throw ValidationException::withMessages([
                        'detail' => ['Stok barang ' . $barang->nama . ' tidak mencukupi.'],
                    ]);
                }
            }

            $penjualan = Penjualan::create([
                'nomor_penjualan' => $this->generateNomor(),
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

        return redirect()->route('transaksi.stok-keluar.index')
            ->with('success', 'Transaksi penjualan berhasil disimpan.');
    }

    public function show(Penjualan $penjualan): View
    {
        $penjualan->load([
            'detail.barang',
            'pelanggan',
            'user',
            'returPenjualan.detail.barang',
            'pembayaranPiutang',
        ]);

        $returByBarang = $penjualan->returPenjualan
            ->flatMap(fn ($retur) => $retur->detail)
            ->groupBy('barang_id')
            ->map(fn ($items) => $items->sum('jumlah'));

        $totalRetur = (float) $penjualan->returPenjualan->sum('total_retur');
        $effectiveTotal = max(0, (float) $penjualan->total - $totalRetur);

        return view('transaksi.penjualan.show', compact(
            'penjualan',
            'returByBarang',
            'totalRetur',
            'effectiveTotal'
        ));
    }

    public function edit(Penjualan $penjualan): View
    {
        $penjualan->load(['detail', 'returPenjualan']);

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
        $newlyCreatedIds = collect($validated['newly_created_barang_ids'] ?? []);

        DB::transaction(function () use ($validated, $detailRows, $newDetails, $ringkasanPembayaran, $penjualan, $userId, $newlyCreatedIds) {
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

                if ($stokBaru < 0 && !$newlyCreatedIds->contains($barangId)) {
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

                if ($delta !== 0) {
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
            }
        });

        return redirect()->route('transaksi.stok-keluar.index')
            ->with('success', 'Transaksi penjualan berhasil diperbarui.');
    }

    public function destroy(Penjualan $penjualan): RedirectResponse
    {
        return redirect()->route('transaksi.stok-keluar.index')
            ->with('error', 'Transaksi penjualan yang sudah tersimpan tidak dapat dihapus.');
    }

    private function generateNomor(): string
    {
        $prefix = 'PJL-' . now()->format('Ymd') . '-';
        $last = Penjualan::query()
            ->where('nomor_penjualan', 'like', $prefix . '%')
            ->orderByDesc('nomor_penjualan')
            ->lockForUpdate()
            ->value('nomor_penjualan');
        $next = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;
        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
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
            'maksHariJatuhTempo' => Pengaturan::maksHariJatuhTempo(),
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

<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Concerns\InteractsWithStok;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStokOpnameRequest;
use App\Models\Barang;
use App\Models\StokMutasi;
use App\Models\StokOpname;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StokOpnameController extends Controller
{
    use InteractsWithStok;

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $tanggal = $request->string('tanggal')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['nomor_opname', 'tanggal'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }

        $stokOpname = StokOpname::query()
            ->with(['user'])
            ->withCount('detail')
            ->when($q !== '', fn ($query) => $query->where('nomor_opname', 'like', '%' . $q . '%'))
            ->when($tanggal !== '', fn ($query) => $query->where('tanggal', $tanggal))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('inventory.stok-opname.index', compact('stokOpname', 'q', 'tanggal', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create(): View
    {
        return view('inventory.stok-opname.create', [
            'barangList' => Barang::query()
                ->with(['kategori', 'satuan', 'merek'])
                ->aktif()
                ->orderBy('nama')
                ->get(),
            'nomorOpname' => $this->generateNomor(),
        ]);
    }

    public function store(StoreStokOpnameRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = (int) auth()->id();

        // Hanya proses baris yang stok_fisik-nya diisi
        $detailDiisi = collect($validated['detail'])->filter(fn ($item) => $item['stok_fisik'] !== null && $item['stok_fisik'] !== '')->values();

        if ($detailDiisi->isEmpty()) {
            return back()->withInput()->with('error', 'Minimal satu barang harus diisi stok fisiknya.');
        }

        DB::transaction(function () use ($validated, $detailDiisi, $userId) {
            $stokOpname = StokOpname::create([
                'nomor_opname' => $this->generateNomor(),
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => $userId,
            ]);

            $barangIds = $detailDiisi->pluck('barang_id')->unique()->sort()->values();

            $barangMap = Barang::query()
                ->whereIn('id', $barangIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($detailDiisi as $item) {
                $barang = $barangMap->get($item['barang_id']);

                $stokSistem = (int) $barang->stok;
                $stokFisik = (int) $item['stok_fisik'];
                $selisih = $stokFisik - $stokSistem;

                $stokOpname->detail()->create([
                    'barang_id' => $barang->id,
                    'stok_sistem' => $stokSistem,
                    'stok_fisik' => $stokFisik,
                    'selisih' => $selisih,
                    'alasan' => $item['alasan'] ?? null,
                ]);

                if ($selisih !== 0) {
                    $barang->update([
                        'stok' => $stokFisik,
                    ]);

                    $this->catatMutasiStok(
                        $barang,
                        StokMutasi::TIPE_PENYESUAIAN,
                        StokMutasi::SUMBER_STOCK_OPNAME,
                        $stokOpname->id,
                        abs($selisih),
                        $stokSistem,
                        $stokFisik,
                        $userId,
                        $item['alasan'] ?? 'Penyesuaian stok dari stock opname.'
                    );
                }
            }
        });

        return redirect()->route('inventory.stok-opname.index')
            ->with('success', 'Stock opname berhasil disimpan.');
    }

    public function show(StokOpname $stokOpname): View
    {
        $stokOpname->load(['detail.barang', 'user']);
        return view('inventory.stok-opname.show', compact('stokOpname'));
    }

    public function edit(StokOpname $stokOpname): RedirectResponse
    {
        return redirect()->route('inventory.stok-opname.index')
            ->with('error', 'Data stock opname yang sudah disimpan tidak dapat diubah.');
    }

    public function destroy(StokOpname $stokOpname): RedirectResponse
    {
        return redirect()->route('inventory.stok-opname.index')
            ->with('error', 'Data stock opname yang sudah disimpan tidak dapat dihapus.');
    }

    private function generateNomor(): string
    {
        $prefix = 'OPN-' . now()->format('Ymd') . '-';
        $last = StokOpname::query()
            ->where('nomor_opname', 'like', $prefix . '%')
            ->orderByDesc('nomor_opname')
            ->lockForUpdate()
            ->value('nomor_opname');
        $next = $last ? ((int) substr($last, strlen($prefix)) + 1) : 1;
        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}

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

        $stokOpname = StokOpname::query()
            ->with(['user'])
            ->withCount('detail')
            ->when($q !== '', fn ($query) => $query->where('nomor_opname', 'like', '%' . $q . '%'))
            ->when($tanggal !== '', fn ($query) => $query->whereDate('tanggal', $tanggal))
            ->latest('tanggal')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('inventory.stok-opname.index', compact('stokOpname', 'q', 'tanggal'));
    }

    public function create(): View
    {
        return view('inventory.stok-opname.create', [
            'barangList' => Barang::query()
                ->with(['kategori', 'satuan', 'merek'])
                ->aktif()
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function store(StoreStokOpnameRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $userId) {
            $stokOpname = StokOpname::create([
                'nomor_opname' => $validated['nomor_opname'],
                'tanggal' => $validated['tanggal'],
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => $userId,
            ]);

            $barangIds = collect($validated['detail'])->pluck('barang_id')->unique()->sort()->values();

            $barangMap = Barang::query()
                ->whereIn('id', $barangIds)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($validated['detail'] as $item) {
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
}

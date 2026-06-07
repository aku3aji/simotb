<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Concerns\InteractsWithStok;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreBarangRequest;
use App\Http\Requests\MasterData\UpdateBarangRequest;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Merek;
use App\Models\Satuan;
use App\Models\StokMutasi;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BarangController extends Controller
{
    use InteractsWithStok;

    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();

        $barang = Barang::query()
            ->with(['kategori', 'satuan', 'merek'])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($subQuery) use ($q) {
                    $subQuery->where('kode_barang', 'like', '%' . $q . '%')
                        ->orWhere('nama', 'like', '%' . $q . '%')
                        ->orWhereHas('kategori', fn ($kategori) => $kategori->where('nama', 'like', '%' . $q . '%'))
                        ->orWhereHas('merek', fn ($merek) => $merek->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('master-data.barang.index', compact('barang', 'q'));
    }

    public function create(): View
    {
        return view('master-data.barang.create', $this->formData());
    }

    public function store(StoreBarangRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['stok'] = $data['stok'] ?? 0;

        DB::transaction(function () use ($data) {
            $barang = Barang::create($data);

            if ((int) $barang->stok > 0) {
                $this->catatMutasiStok(
                    $barang,
                    StokMutasi::TIPE_MASUK,
                    StokMutasi::SUMBER_MANUAL,
                    $barang->id,
                    (int) $barang->stok,
                    0,
                    (int) $barang->stok,
                    (int) auth()->id(),
                    'Stok awal barang saat data dibuat.'
                );
            }
        });

        return redirect()->route('master-data.barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Barang $barang): View
    {
        return view('master-data.barang.edit', array_merge(
            $this->formData(),
            compact('barang')
        ));
    }

    public function update(UpdateBarangRequest $request, Barang $barang): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        $isOwner = auth()->user()->isOwner();
        $data['stok'] = ($isOwner && isset($data['stok'])) ? $data['stok'] : $barang->stok;

        $stokSebelum = (int) $barang->stok;
        $stokSesudah = (int) $data['stok'];

        DB::transaction(function () use ($barang, $data, $stokSebelum, $stokSesudah) {
            $barang->update($data);

            if ($stokSebelum !== $stokSesudah) {
                $this->catatMutasiStok(
                    $barang,
                    StokMutasi::TIPE_PENYESUAIAN,
                    StokMutasi::SUMBER_MANUAL,
                    $barang->id,
                    abs($stokSesudah - $stokSebelum),
                    $stokSebelum,
                    $stokSesudah,
                    (int) auth()->id(),
                    'Penyesuaian stok manual dari master data barang.'
                );
            }
        });

        return redirect()->route('master-data.barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang): RedirectResponse
    {
        try {
            $barang->delete();
        } catch (QueryException) {
            return back()->with('error', 'Barang tidak dapat dihapus karena sudah digunakan dalam transaksi.');
        }

        return redirect()->route('master-data.barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $jumlah = Barang::whereIn('id', $ids)->delete();

            return redirect()->route('master-data.barang.index')
                ->with('success', $jumlah . ' barang berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data transaksi.');
        }
    }

    private function formData(): array
    {
        return [
            'kategoriList' => Kategori::query()->orderBy('nama')->get(),
            'satuanList' => Satuan::query()->orderBy('nama')->get(),
            'merekList' => Merek::query()->orderBy('nama')->get(),
        ];
    }
}

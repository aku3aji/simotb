<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\StokMutasi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MutasiStokController extends Controller
{
    public function index(Request $request): View
    {
        $q          = $request->string('q')->trim()->toString();
        $kategoriId = $request->integer('kategori_id') ?: null;
        $perPage    = min(50, max(10, (int) $request->input('per_page', 10)));

        $barang = Barang::query()
            ->with(['kategori', 'satuan'])
            ->withCount('stokMutasi')
            ->when($q !== '', fn ($b) => $b->where('nama', 'like', '%'.$q.'%')->orWhere('kode_barang', 'like', '%'.$q.'%'))
            ->when($kategoriId, fn ($b) => $b->where('kategori_id', $kategoriId))
            ->orderBy('nama')
            ->paginate($perPage);
        $barang->appends(request()->query());

        $kategoriList = Kategori::query()->orderBy('nama')->get(['id', 'nama']);

        return view('inventory.mutasi-stok.index', compact('barang', 'kategoriList', 'q', 'kategoriId', 'perPage'));
    }

    public function show(Request $request, Barang $barang): View
    {
        $tipe           = $request->string('tipe')->trim()->toString();
        $sumber         = $request->string('sumber')->trim()->toString();
        $tanggalMulai   = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();
        $perPage        = min(50, max(10, (int) $request->input('per_page', 10)));

        $allowedTipe   = [StokMutasi::TIPE_MASUK, StokMutasi::TIPE_KELUAR, StokMutasi::TIPE_PENYESUAIAN];
        $allowedSumber = [
            StokMutasi::SUMBER_PEMBELIAN,
            StokMutasi::SUMBER_PENJUALAN,
            StokMutasi::SUMBER_RETUR_PENJUALAN,
            StokMutasi::SUMBER_STOCK_OPNAME,
            StokMutasi::SUMBER_MANUAL,
        ];

        $mutasi = StokMutasi::query()
            ->with('user')
            ->where('barang_id', $barang->id)
            ->when(in_array($tipe, $allowedTipe), fn ($q) => $q->where('tipe', $tipe))
            ->when(in_array($sumber, $allowedSumber), fn ($q) => $q->where('sumber', $sumber))
            ->when($tanggalMulai !== '', fn ($q) => $q->where('created_at', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($q) => $q->where('created_at', '<=', $tanggalSelesai . ' 23:59:59'))
            ->latest('id')
            ->paginate($perPage);
        $mutasi->appends(request()->query());

        return view('inventory.mutasi-stok.show', compact(
            'barang',
            'mutasi',
            'tipe',
            'sumber',
            'tanggalMulai',
            'tanggalSelesai',
            'perPage',
        ));
    }
}

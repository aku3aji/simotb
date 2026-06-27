<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StorePelangganRequest;
use App\Http\Requests\MasterData\UpdatePelangganRequest;
use App\Models\Pelanggan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PelangganController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['nama'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nama';
        }

        $pelanggan = Pelanggan::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('telepon', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%');
            })
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('master-data.pelanggan.index', compact('pelanggan', 'q', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create(): View
    {
        return view('master-data.pelanggan.create');
    }

    public function store(StorePelangganRequest $request): RedirectResponse
    {
        Pelanggan::create($request->validated());

        return redirect()->route('master-data.pelanggan.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show(Pelanggan $pelanggan): View
    {
        $penjualan = $pelanggan->penjualan()
            ->with(['user'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $stats = [
            'total_transaksi' => $penjualan->count(),
            'total_belanja'   => $penjualan->sum('total'),
            'sisa_piutang'    => $penjualan->sum('sisa_piutang'),
        ];

        return view('master-data.pelanggan.show', compact('pelanggan', 'penjualan', 'stats'));
    }

    public function edit(Pelanggan $pelanggan): View
    {
        return view('master-data.pelanggan.edit', compact('pelanggan'));
    }

    public function update(UpdatePelangganRequest $request, Pelanggan $pelanggan): RedirectResponse
    {
        $pelanggan->update($request->validated());

        return redirect()->route('master-data.pelanggan.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    public function destroy(Pelanggan $pelanggan): RedirectResponse
    {
        try {
            $pelanggan->delete();
        } catch (QueryException) {
            return back()->with('error', 'Pelanggan tidak dapat dihapus karena sudah digunakan.');
        }

        return redirect()->route('master-data.pelanggan.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $jumlah = Pelanggan::whereIn('id', $ids)->delete();

            return redirect()->route('master-data.pelanggan.index')
                ->with('success', $jumlah . ' pelanggan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data lain.');
        }
    }
}

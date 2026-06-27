<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreVendorRequest;
use App\Http\Requests\MasterData\UpdateVendorRequest;
use App\Models\Vendor;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
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

        $vendor = Vendor::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('telepon', 'like', '%' . $q . '%')
                    ->orWhere('email', 'like', '%' . $q . '%')
                    ->orWhere('kontak_person', 'like', '%' . $q . '%');
            })
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('master-data.vendor.index', compact('vendor', 'q', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create(): View
    {
        return view('master-data.vendor.create');
    }

    public function store(StoreVendorRequest $request): RedirectResponse
    {
        Vendor::create($request->validated());

        return redirect()->route('master-data.vendor.index')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function show(Vendor $vendor): View
    {
        $pembelian = $vendor->pembelian()
            ->with(['user'])
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $stats = [
            'total_transaksi' => $pembelian->count(),
            'total_pembelian' => $pembelian->sum('total'),
        ];

        return view('master-data.vendor.show', compact('vendor', 'pembelian', 'stats'));
    }

    public function edit(Vendor $vendor): View
    {
        return view('master-data.vendor.edit', compact('vendor'));
    }

    public function update(UpdateVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->update($request->validated());

        return redirect()->route('master-data.vendor.index')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        try {
            $vendor->delete();
        } catch (QueryException) {
            return back()->with('error', 'Vendor tidak dapat dihapus karena sudah digunakan.');
        }

        return redirect()->route('master-data.vendor.index')
            ->with('success', 'Vendor berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $jumlah = Vendor::whereIn('id', $ids)->delete();

            return redirect()->route('master-data.vendor.index')
                ->with('success', $jumlah . ' vendor berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data lain.');
        }
    }
}

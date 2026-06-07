<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreSatuanRequest;
use App\Http\Requests\MasterData\UpdateSatuanRequest;
use App\Models\Satuan;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SatuanController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();

        $satuan = Satuan::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('singkatan', 'like', '%' . $q . '%');
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('master-data.satuan.index', compact('satuan', 'q'));
    }

    public function create(): View
    {
        return view('master-data.satuan.create');
    }

    public function store(StoreSatuanRequest $request): RedirectResponse
    {
        Satuan::create($request->validated());

        return redirect()->route('master-data.satuan.index')
            ->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Satuan $satuan): View
    {
        return view('master-data.satuan.edit', compact('satuan'));
    }

    public function update(UpdateSatuanRequest $request, Satuan $satuan): RedirectResponse
    {
        $satuan->update($request->validated());

        return redirect()->route('master-data.satuan.index')
            ->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Satuan $satuan): RedirectResponse
    {
        try {
            $satuan->delete();
        } catch (QueryException) {
            return back()->with('error', 'Satuan tidak dapat dihapus karena sudah digunakan.');
        }

        return redirect()->route('master-data.satuan.index')
            ->with('success', 'Satuan berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $jumlah = Satuan::whereIn('id', $ids)->delete();

            return redirect()->route('master-data.satuan.index')
                ->with('success', $jumlah . ' satuan berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data lain.');
        }
    }
}

<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Http\Requests\MasterData\StoreMerekRequest;
use App\Http\Requests\MasterData\UpdateMerekRequest;
use App\Models\Merek;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MerekController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['nama', 'created_at'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nama';
        }

        $merek = Merek::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('deskripsi', 'like', '%' . $q . '%');
            })
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('master-data.merek.index', compact('merek', 'q', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create(): View
    {
        return view('master-data.merek.create');
    }

    public function store(StoreMerekRequest $request): RedirectResponse
    {
        Merek::create($request->validated());

        return redirect()->route('master-data.merek.index')
            ->with('success', 'Merek berhasil ditambahkan.');
    }

    public function edit(Merek $merek): View
    {
        return view('master-data.merek.edit', compact('merek'));
    }

    public function update(UpdateMerekRequest $request, Merek $merek): RedirectResponse
    {
        $merek->update($request->validated());

        return redirect()->route('master-data.merek.index')
            ->with('success', 'Merek berhasil diperbarui.');
    }

    public function destroy(Merek $merek): RedirectResponse
    {
        try {
            $merek->delete();
        } catch (QueryException) {
            return back()->with('error', 'Merek tidak dapat dihapus karena sudah digunakan.');
        }

        return redirect()->route('master-data.merek.index')
            ->with('success', 'Merek berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $jumlah = Merek::whereIn('id', $ids)->delete();

            return redirect()->route('master-data.merek.index')
                ->with('success', $jumlah . ' merek berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data lain.');
        }
    }
}

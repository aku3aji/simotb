<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pegawai\StorePegawaiRequest;
use App\Http\Requests\Pegawai\UpdatePegawaiRequest;
use App\Models\Pegawai;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PegawaiController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'asc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['nama', 'jabatan', 'tanggal_masuk'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'nama';
        }

        $pegawai = Pegawai::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%')
                    ->orWhere('jabatan', 'like', '%' . $q . '%')
                    ->orWhere('telepon', 'like', '%' . $q . '%');
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('pegawai.pegawai.index', compact('pegawai', 'q', 'status', 'sortBy', 'sortDir', 'perPage'));
    }

    public function create(): View
    {
        return view('pegawai.pegawai.create');
    }

    public function store(StorePegawaiRequest $request): RedirectResponse
    {
        Pegawai::create($request->validated());

        return redirect()->route('pegawai.pegawai.index')
            ->with('success', 'Data pegawai berhasil ditambahkan.');
    }

    public function edit(Pegawai $pegawai): View
    {
        return view('pegawai.pegawai.edit', compact('pegawai'));
    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai): RedirectResponse
    {
        $pegawai->update($request->validated());

        return redirect()->route('pegawai.pegawai.index')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(Pegawai $pegawai): RedirectResponse
    {
        try {
            $pegawai->delete();
        } catch (QueryException) {
            return back()->with('error', 'Pegawai tidak dapat dihapus karena sudah digunakan.');
        }

        return redirect()->route('pegawai.pegawai.index')
            ->with('success', 'Data pegawai berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        try {
            $jumlah = Pegawai::whereIn('id', $ids)->delete();

            return redirect()->route('pegawai.pegawai.index')
                ->with('success', $jumlah . ' data pegawai berhasil dihapus.');
        } catch (\Illuminate\Database\QueryException) {
            return back()->with('error', 'Sebagian data tidak dapat dihapus karena masih digunakan oleh data lain.');
        }
    }
}

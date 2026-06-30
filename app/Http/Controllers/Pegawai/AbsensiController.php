<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pegawai\UpdateAbsensiRequest;
use App\Models\Absensi;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AbsensiController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $status = $request->string('status')->trim()->toString();
        $tanggal = $request->string('tanggal')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['tanggal', 'status'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }

        $absensi = Absensi::query()
            ->with(['pegawai', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('pegawai', fn ($pegawai) => $pegawai->where('nama', 'like', '%' . $q . '%'));
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($tanggal !== '', fn ($query) => $query->where('tanggal', $tanggal))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('pegawai.absensi.index', compact('absensi', 'q', 'status', 'tanggal', 'sortBy', 'sortDir', 'perPage'));
    }

    public function edit(Absensi $absensi): View
    {
        return view('pegawai.absensi.edit', [
            'absensi' => $absensi,
            'pegawaiList' => Pegawai::query()
                ->where('status', Pegawai::STATUS_AKTIF)
                ->orWhere('id', $absensi->pegawai_id)
                ->orderBy('nama')
                ->get(),
        ]);
    }

    public function update(UpdateAbsensiRequest $request, Absensi $absensi): RedirectResponse
    {
        $absensi->update($request->validated() + [
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('pegawai.absensi.index')
            ->with('success', 'Data absensi berhasil diperbarui.');
    }

    public function cataMassal(): View
    {
        return view('pegawai.absensi.catat-massal', [
            'pegawaiList' => Pegawai::query()
                ->where('status', Pegawai::STATUS_AKTIF)
                ->orderBy('nama')
                ->get(),
            'tanggal' => now()->format('Y-m-d'),
        ]);
    }

    public function storeMassal(Request $request): RedirectResponse
    {
        $tanggal = $request->input('tanggal', now()->format('Y-m-d'));
        $rows    = $request->input('absensi', []);
        $userId  = auth()->id();
        $saved   = 0;
        $skipped = 0;

        $validStatus = [Absensi::STATUS_HADIR, Absensi::STATUS_IZIN, Absensi::STATUS_SAKIT, Absensi::STATUS_ALPHA];

        DB::transaction(function () use ($rows, $tanggal, $userId, $validStatus, &$saved, &$skipped) {
            foreach ($rows as $pegawaiId => $data) {
                $pegawaiId = (int) $pegawaiId;
                if ($pegawaiId <= 0) {
                    continue;
                }

                $sudahAda = Absensi::where('pegawai_id', $pegawaiId)->where('tanggal', $tanggal)->exists();
                if ($sudahAda) {
                    $skipped++;
                    continue;
                }

                $status = in_array($data['status'] ?? '', $validStatus) ? $data['status'] : Absensi::STATUS_HADIR;
                $isHadir = $status === Absensi::STATUS_HADIR;

                Absensi::create([
                    'pegawai_id' => $pegawaiId,
                    'tanggal'    => $tanggal,
                    'status'     => $status,
                    'jam_masuk'  => $isHadir ? ($data['jam_masuk'] ?: null) : null,
                    'jam_keluar' => $isHadir ? ($data['jam_keluar'] ?: null) : null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'user_id'    => $userId,
                ]);
                $saved++;
            }
        });

        $pesan = "Berhasil menyimpan {$saved} data absensi.";
        if ($skipped > 0) {
            $pesan .= " {$skipped} pegawai dilewati karena sudah ada absensi untuk tanggal ini.";
        }

        return redirect()->route('pegawai.absensi.index')->with('success', $pesan);
    }

    public function destroy(Absensi $absensi): RedirectResponse
    {
        $absensi->delete();

        return redirect()->route('pegawai.absensi.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        $jumlah = Absensi::whereIn('id', $ids)->delete();

        return redirect()->route('pegawai.absensi.index')
            ->with('success', $jumlah . ' data absensi berhasil dihapus.');
    }
}

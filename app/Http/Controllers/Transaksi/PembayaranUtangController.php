<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePembayaranUtangRequest;
use App\Http\Requests\Transaksi\UpdatePembayaranUtangRequest;
use App\Models\PembayaranUtang;
use App\Models\Pembelian;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranUtangController extends Controller
{
    public function index(Request $request): View
    {
        $q = $request->string('q')->trim()->toString();
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();
        $sortBy = $request->string('sort')->trim()->toString();
        $sortDir = $request->string('dir')->trim()->toString();
        $sortDir = in_array($sortDir, ['asc', 'desc']) ? $sortDir : 'desc';
        $perPage = min(100, max(10, (int) $request->input('per_page', 10)));
        $allowedSorts = ['tanggal'];
        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'tanggal';
        }

        $utangOutstanding = Pembelian::query()
            ->with(['vendor', 'pembayaranUtang'])
            ->kredit()
            ->belumLunas()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_pembelian', 'like', '%' . $q . '%')
                        ->orWhereHas('vendor', fn ($v) => $v->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tanggalMulai !== '', fn ($query) => $query->where('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->where('tanggal', '<=', $tanggalSelesai))
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $totalUtang = $utangOutstanding->sum('sisa_utang');

        $pembayaranUtang = PembayaranUtang::query()
            ->with(['pembelian.vendor', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('pembelian', function ($pembelian) use ($q) {
                    $pembelian->where('nomor_pembelian', 'like', '%' . $q . '%')
                        ->orWhereHas('vendor', fn ($v) => $v->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tanggalMulai !== '', fn ($query) => $query->where('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->where('tanggal', '<=', $tanggalSelesai))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('transaksi.pembayaran-utang.index', compact(
            'utangOutstanding',
            'totalUtang',
            'pembayaranUtang',
            'q',
            'tanggalMulai',
            'tanggalSelesai',
            'sortBy',
            'sortDir',
            'perPage'
        ));
    }

    public function show(Pembelian $pembelian): View
    {
        $pembelian->load(['vendor', 'pembayaranUtang.user']);

        return view('transaksi.pembayaran-utang.show', compact('pembelian'));
    }

    public function create(Request $request): View
    {
        return view('transaksi.pembayaran-utang.create', [
            'pembelianList'      => $this->availablePembelian(),
            'selectedPembelianId' => $request->integer('pembelian_id'),
        ]);
    }

    public function store(StorePembayaranUtangRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $userId) {
            $pembelian = Pembelian::query()
                ->lockForUpdate()
                ->findOrFail($validated['pembelian_id']);

            $jumlahBayar = (float) $validated['jumlah_bayar'];

            if ($jumlahBayar > (float) $pembelian->sisa_utang) {
                throw ValidationException::withMessages([
                    'jumlah_bayar' => ['Jumlah bayar melebihi sisa utang.'],
                ]);
            }

            PembayaranUtang::create([
                'pembelian_id' => $pembelian->id,
                'tanggal' => $validated['tanggal'],
                'jumlah_bayar' => $jumlahBayar,
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => $userId,
            ]);

            $totalDibayar = (float) PembayaranUtang::where('pembelian_id', $pembelian->id)->sum('jumlah_bayar');
            $this->syncPaymentSummary($pembelian, $totalDibayar);
        });

        return redirect()->route('transaksi.pembayaran-utang.index')
            ->with('success', 'Pembayaran utang berhasil disimpan.');
    }

    public function edit(PembayaranUtang $pembayaranUtang): View
    {
        return view('transaksi.pembayaran-utang.edit', [
            'pembayaranUtang' => $pembayaranUtang,
            'pembelianList' => $this->availablePembelian($pembayaranUtang->pembelian_id),
        ]);
    }

    public function update(UpdatePembayaranUtangRequest $request, PembayaranUtang $pembayaranUtang): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['pembelian_id'] !== (int) $pembayaranUtang->pembelian_id) {
            return back()->withInput()
                ->with('error', 'Pembayaran utang tidak dapat dipindahkan ke transaksi stok masuk lain.');
        }

        DB::transaction(function () use ($validated, $pembayaranUtang) {
            $pembelian = Pembelian::query()
                ->lockForUpdate()
                ->findOrFail($pembayaranUtang->pembelian_id);

            $jumlahBaru = (float) $validated['jumlah_bayar'];

            $pembayaranUtang->update([
                'tanggal' => $validated['tanggal'],
                'jumlah_bayar' => $jumlahBaru,
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => (int) auth()->id(),
            ]);

            $dibayarBaru = (float) PembayaranUtang::where('pembelian_id', $pembelian->id)->sum('jumlah_bayar');

            if ($dibayarBaru > (float) $pembelian->total) {
                throw ValidationException::withMessages([
                    'jumlah_bayar' => ['Jumlah bayar melebihi total stok masuk.'],
                ]);
            }

            $this->syncPaymentSummary($pembelian, $dibayarBaru);
        });

        return redirect()->route('transaksi.pembayaran-utang.index')
            ->with('success', 'Pembayaran utang berhasil diperbarui.');
    }

    public function destroy(PembayaranUtang $pembayaranUtang): RedirectResponse
    {
        DB::transaction(function () use ($pembayaranUtang) {
            $pembelian = Pembelian::query()
                ->lockForUpdate()
                ->findOrFail($pembayaranUtang->pembelian_id);

            $pembayaranUtang->delete();

            $dibayarBaru = (float) PembayaranUtang::where('pembelian_id', $pembelian->id)->sum('jumlah_bayar');
            $this->syncPaymentSummary($pembelian, $dibayarBaru);
        });

        return redirect()->route('transaksi.pembayaran-utang.index')
            ->with('success', 'Pembayaran utang berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        DB::transaction(function () use ($ids) {
            $payments = PembayaranUtang::with('pembelian')->whereIn('id', $ids)->get();
            $pembelianIds = $payments->pluck('pembelian_id')->unique();

            PembayaranUtang::whereIn('id', $ids)->delete();

            $dibayarMap = PembayaranUtang::whereIn('pembelian_id', $pembelianIds)
                ->selectRaw('pembelian_id, SUM(jumlah_bayar) as total')
                ->groupBy('pembelian_id')
                ->pluck('total', 'pembelian_id');

            Pembelian::whereIn('id', $pembelianIds)->each(function ($pembelian) use ($dibayarMap) {
                $dibayar = (float) ($dibayarMap->get($pembelian->id) ?? 0);
                $this->syncPaymentSummary($pembelian, $dibayar);
            });
        });

        return redirect()->route('transaksi.pembayaran-utang.index')
            ->with('success', count($ids) . ' pembayaran utang berhasil dihapus.');
    }

    private function availablePembelian(?int $currentPembelianId = null)
    {
        return Pembelian::query()
            ->with('vendor')
            ->where('tipe_pembayaran', Pembelian::TIPE_KREDIT)
            ->where(function ($query) use ($currentPembelianId) {
                $query->where('sisa_utang', '>', 0);

                if ($currentPembelianId) {
                    $query->orWhere('id', $currentPembelianId);
                }
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    private function syncPaymentSummary(Pembelian $pembelian, float $dibayar): void
    {
        $sisaUtang = max(0, (float) $pembelian->total - $dibayar);

        $pembelian->update([
            'dibayar' => $dibayar,
            'sisa_utang' => $sisaUtang,
            'status_pembayaran' => $dibayar <= 0
                ? Pembelian::STATUS_BELUM_LUNAS
                : ($sisaUtang > 0 ? Pembelian::STATUS_SEBAGIAN : Pembelian::STATUS_LUNAS),
        ]);
    }
}

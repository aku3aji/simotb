<?php

namespace App\Http\Controllers\Transaksi;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaksi\StorePembayaranPiutangRequest;
use App\Http\Requests\Transaksi\UpdatePembayaranPiutangRequest;
use App\Models\PembayaranPiutang;
use App\Models\Penjualan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PembayaranPiutangController extends Controller
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

        $piutangOutstanding = Penjualan::query()
            ->with(['pelanggan', 'pembayaranPiutang'])
            ->kredit()
            ->belumLunas()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('nomor_penjualan', 'like', '%' . $q . '%')
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tanggalMulai !== '', fn ($query) => $query->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->whereDate('tanggal', '<=', $tanggalSelesai))
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $totalPiutang = $piutangOutstanding->sum('sisa_piutang');

        $pembayaranPiutang = PembayaranPiutang::query()
            ->with(['penjualan.pelanggan', 'user'])
            ->when($q !== '', function ($query) use ($q) {
                $query->whereHas('penjualan', function ($penjualan) use ($q) {
                    $penjualan->where('nomor_penjualan', 'like', '%' . $q . '%')
                        ->orWhereHas('pelanggan', fn ($p) => $p->where('nama', 'like', '%' . $q . '%'));
                });
            })
            ->when($tanggalMulai !== '', fn ($query) => $query->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($query) => $query->whereDate('tanggal', '<=', $tanggalSelesai))
            ->orderBy($sortBy, $sortDir)
            ->orderBy('id', $sortDir)
            ->paginate($perPage)
            ->withQueryString();

        return view('transaksi.pembayaran-piutang.index', compact(
            'piutangOutstanding',
            'totalPiutang',
            'pembayaranPiutang',
            'q',
            'tanggalMulai',
            'tanggalSelesai',
            'sortBy',
            'sortDir',
            'perPage'
        ));
    }

    public function create(Request $request): View
    {
        return view('transaksi.pembayaran-piutang.create', [
            'penjualanList'      => $this->availablePenjualan(),
            'selectedPenjualanId' => $request->integer('penjualan_id'),
        ]);
    }

    public function store(StorePembayaranPiutangRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = (int) auth()->id();

        DB::transaction(function () use ($validated, $userId) {
            $penjualan = Penjualan::query()
                ->lockForUpdate()
                ->findOrFail($validated['penjualan_id']);

            $jumlahBayar = (float) $validated['jumlah_bayar'];

            if ($jumlahBayar > (float) $penjualan->sisa_piutang) {
                throw ValidationException::withMessages([
                    'jumlah_bayar' => ['Jumlah bayar melebihi sisa piutang.'],
                ]);
            }

            PembayaranPiutang::create([
                'penjualan_id' => $penjualan->id,
                'tanggal' => $validated['tanggal'],
                'jumlah_bayar' => $jumlahBayar,
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => $userId,
            ]);

            $totalDibayar = (float) PembayaranPiutang::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
            $this->syncPaymentSummary($penjualan, $totalDibayar);
        });

        return redirect()->route('transaksi.pembayaran-piutang.index')
            ->with('success', 'Pembayaran piutang berhasil disimpan.');
    }

    public function edit(PembayaranPiutang $pembayaranPiutang): View
    {
        return view('transaksi.pembayaran-piutang.edit', [
            'pembayaranPiutang' => $pembayaranPiutang,
            'penjualanList' => $this->availablePenjualan($pembayaranPiutang->penjualan_id),
        ]);
    }

    public function update(UpdatePembayaranPiutangRequest $request, PembayaranPiutang $pembayaranPiutang): RedirectResponse
    {
        $validated = $request->validated();

        if ((int) $validated['penjualan_id'] !== (int) $pembayaranPiutang->penjualan_id) {
            return back()->withInput()
                ->with('error', 'Pembayaran piutang tidak dapat dipindahkan ke transaksi penjualan lain.');
        }

        DB::transaction(function () use ($validated, $pembayaranPiutang) {
            $penjualan = Penjualan::query()
                ->lockForUpdate()
                ->findOrFail($pembayaranPiutang->penjualan_id);

            $jumlahBaru = (float) $validated['jumlah_bayar'];

            $pembayaranPiutang->update([
                'tanggal' => $validated['tanggal'],
                'jumlah_bayar' => $jumlahBaru,
                'metode_pembayaran' => $validated['metode_pembayaran'] ?? null,
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => (int) auth()->id(),
            ]);

            $dibayarBaru = (float) PembayaranPiutang::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');

            if ($dibayarBaru > (float) $penjualan->total) {
                throw ValidationException::withMessages([
                    'jumlah_bayar' => ['Jumlah bayar melebihi total penjualan.'],
                ]);
            }

            $this->syncPaymentSummary($penjualan, $dibayarBaru);
        });

        return redirect()->route('transaksi.pembayaran-piutang.index')
            ->with('success', 'Pembayaran piutang berhasil diperbarui.');
    }

    public function destroy(PembayaranPiutang $pembayaranPiutang): RedirectResponse
    {
        DB::transaction(function () use ($pembayaranPiutang) {
            $penjualan = Penjualan::query()
                ->lockForUpdate()
                ->findOrFail($pembayaranPiutang->penjualan_id);

            $pembayaranPiutang->delete();

            $dibayarBaru = (float) PembayaranPiutang::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
            $this->syncPaymentSummary($penjualan, $dibayarBaru);
        });

        return redirect()->route('transaksi.pembayaran-piutang.index')
            ->with('success', 'Pembayaran piutang berhasil dihapus.');
    }

    public function destroyBulk(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])), fn ($id) => $id > 0);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada data yang dipilih.');
        }

        DB::transaction(function () use ($ids) {
            $payments = PembayaranPiutang::with('penjualan')->whereIn('id', $ids)->get();
            $penjualanIds = $payments->pluck('penjualan_id')->unique();

            PembayaranPiutang::whereIn('id', $ids)->delete();

            Penjualan::whereIn('id', $penjualanIds)->each(function ($penjualan) {
                $dibayar = (float) PembayaranPiutang::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
                $this->syncPaymentSummary($penjualan, $dibayar);
            });
        });

        return redirect()->route('transaksi.pembayaran-piutang.index')
            ->with('success', count($ids) . ' pembayaran piutang berhasil dihapus.');
    }

    private function availablePenjualan(?int $currentPenjualanId = null)
    {
        return Penjualan::query()
            ->with('pelanggan')
            ->where('tipe_pembayaran', Penjualan::TIPE_KREDIT)
            ->where(function ($query) use ($currentPenjualanId) {
                $query->where('sisa_piutang', '>', 0);

                if ($currentPenjualanId) {
                    $query->orWhere('id', $currentPenjualanId);
                }
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->get();
    }

    private function syncPaymentSummary(Penjualan $penjualan, float $dibayar): void
    {
        $totalRetur = (float) $penjualan->returPenjualan()->sum('total_retur');
        $effectiveTotal = max(0, (float) $penjualan->total - $totalRetur);
        $sisaPiutang = max(0, $effectiveTotal - $dibayar);

        $penjualan->update([
            'dibayar' => $dibayar,
            'sisa_piutang' => $sisaPiutang,
            'status_pembayaran' => $dibayar <= 0
                ? Penjualan::STATUS_BELUM_LUNAS
                : ($sisaPiutang > 0 ? Penjualan::STATUS_SEBAGIAN : Penjualan::STATUS_LUNAS),
        ]);
    }
}

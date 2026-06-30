<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PembayaranPiutang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\ReturPenjualan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        $totalReturBulanIni = (float) ReturPenjualan::query()
            ->whereBetween('tanggal', [$awalBulan->toDateString(), $akhirBulan->toDateString()])
            ->sum('total_retur');

        $totalPenjualan = max(0, (float) Penjualan::query()
            ->whereBetween('tanggal', [$awalBulan->toDateString(), $akhirBulan->toDateString()])
            ->sum('total') - $totalReturBulanIni);

        $totalPembelian = Pembelian::query()
            ->whereBetween('tanggal', [$awalBulan->toDateString(), $akhirBulan->toDateString()])
            ->sum('total');

        $stokMenipis = Barang::query()
            ->aktif()
            ->stokMenipis()
            ->count();

        $totalPiutang = Penjualan::query()
            ->kredit()
            ->sum('sisa_piutang');

        $start = now()->subDays(29)->toDateString();
        $end   = now()->toDateString();

        $chartPenjualan = Penjualan::query()
            ->selectRaw('DATE(tanggal) as hari, SUM(total) as total')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('hari')->get()->keyBy('hari');

        $chartPembelian = Pembelian::query()
            ->selectRaw('DATE(tanggal) as hari, SUM(total) as total')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('hari')->get()->keyBy('hari');

        $chartRetur = ReturPenjualan::query()
            ->selectRaw('DATE(tanggal) as hari, SUM(total_retur) as total')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('hari')->get()->keyBy('hari');

        $chartPiutang = PembayaranPiutang::query()
            ->selectRaw('DATE(tanggal) as hari, SUM(jumlah_bayar) as total')
            ->whereBetween('tanggal', [$start, $end])
            ->groupBy('hari')->get()->keyBy('hari');

        $chartLabels          = [];
        $chartValuesPenjualan = [];
        $chartValuesPembelian = [];
        $chartValuesRetur     = [];
        $chartValuesPiutang   = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $chartLabels[]          = Carbon::parse($date)->format('d/m');
            $chartValuesPenjualan[] = (float) ($chartPenjualan->get($date)?->total ?? 0);
            $chartValuesPembelian[] = (float) ($chartPembelian->get($date)?->total ?? 0);
            $chartValuesRetur[]     = (float) ($chartRetur->get($date)?->total ?? 0);
            $chartValuesPiutang[]   = (float) ($chartPiutang->get($date)?->total ?? 0);
        }

        // Pengingat jatuh tempo: overdue + jatuh tempo dalam 7 hari ke depan.
        $ambangTempo = now()->addDays(7)->toDateString();

        $piutangJatuhTempo = Penjualan::query()
            ->with('pelanggan')
            ->kredit()
            ->belumLunas()
            ->whereNotNull('jatuh_tempo')
            ->whereDate('jatuh_tempo', '<=', $ambangTempo)
            ->orderBy('jatuh_tempo')
            ->limit(8)
            ->get();

        $utangJatuhTempo = Pembelian::query()
            ->with('vendor')
            ->kredit()
            ->belumLunas()
            ->whereNotNull('jatuh_tempo')
            ->whereDate('jatuh_tempo', '<=', $ambangTempo)
            ->orderBy('jatuh_tempo')
            ->limit(8)
            ->get();

        $penjualanTerbaru = Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->latest('tanggal')
            ->latest('id')
            ->limit(5)
            ->get();

        $barangMenipis = Barang::query()
            ->with(['kategori', 'satuan', 'merek'])
            ->aktif()
            ->stokMenipis()
            ->orderBy('stok')
            ->orderBy('nama')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalPenjualan',
            'totalPembelian',
            'totalReturBulanIni',
            'stokMenipis',
            'totalPiutang',
            'piutangJatuhTempo',
            'utangJatuhTempo',
            'penjualanTerbaru',
            'barangMenipis',
            'chartLabels',
            'chartValuesPenjualan',
            'chartValuesPembelian',
            'chartValuesRetur',
            'chartValuesPiutang'
        ));
    }
}

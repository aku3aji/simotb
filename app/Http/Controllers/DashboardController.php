<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $awalBulan = Carbon::now()->startOfMonth();
        $akhirBulan = Carbon::now()->endOfMonth();

        $totalPenjualan = Penjualan::query()
            ->whereBetween('tanggal', [$awalBulan->toDateString(), $akhirBulan->toDateString()])
            ->sum('total');

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
            'stokMenipis',
            'totalPiutang',
            'penjualanTerbaru',
            'barangMenipis'
        ));
    }
}

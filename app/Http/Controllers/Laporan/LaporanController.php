<?php

namespace App\Http\Controllers\Laporan;

use App\Exports\AbsensiExport;
use App\Exports\PembelianExport;
use App\Exports\PenjualanExport;
use App\Exports\PiutangExport;
use App\Exports\StokExport;
use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Pegawai;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function stok(Request $request): mixed
    {
        $kategoriId = $request->integer('kategori_id');

        $barang = Barang::query()
            ->with(['kategori', 'satuan', 'merek'])
            ->when($kategoriId > 0, fn ($q) => $q->where('kategori_id', $kategoriId))
            ->orderBy('nama')
            ->get();

        $kategoriList = Kategori::query()->orderBy('nama')->get();

        $export = $request->query('export');

        if ($export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.stok', compact('barang', 'kategoriId', 'kategoriList'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('laporan-stok-' . now()->format('Ymd') . '.pdf');
        }

        if ($export === 'excel') {
            return Excel::download(
                new StokExport($kategoriId),
                'laporan-stok-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('laporan.stok', compact('barang', 'kategoriId', 'kategoriList'));
    }

    public function pembelian(Request $request): mixed
    {
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();

        $pembelian = Pembelian::query()
            ->with(['vendor', 'user'])
            ->when($tanggalMulai !== '', fn ($q) => $q->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($q) => $q->whereDate('tanggal', '<=', $tanggalSelesai))
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $totalPembelian = $pembelian->sum('total');

        $export = $request->query('export');

        if ($export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.pembelian', compact(
                'pembelian', 'totalPembelian', 'tanggalMulai', 'tanggalSelesai'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('laporan-pembelian-' . now()->format('Ymd') . '.pdf');
        }

        if ($export === 'excel') {
            return Excel::download(
                new PembelianExport($tanggalMulai, $tanggalSelesai),
                'laporan-pembelian-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('laporan.pembelian', compact(
            'pembelian', 'totalPembelian', 'tanggalMulai', 'tanggalSelesai'
        ));
    }

    public function penjualan(Request $request): mixed
    {
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();
        $tipePembayaran = $request->string('tipe_pembayaran')->trim()->toString();

        $penjualan = Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->when($tanggalMulai !== '', fn ($q) => $q->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($q) => $q->whereDate('tanggal', '<=', $tanggalSelesai))
            ->when($tipePembayaran !== '', fn ($q) => $q->where('tipe_pembayaran', $tipePembayaran))
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $totalPenjualan = $penjualan->sum('total');
        $totalTunai = $penjualan->where('tipe_pembayaran', Penjualan::TIPE_TUNAI)->sum('total');
        $totalKredit = $penjualan->where('tipe_pembayaran', Penjualan::TIPE_KREDIT)->sum('total');

        $export = $request->query('export');

        if ($export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.penjualan', compact(
                'penjualan', 'totalPenjualan', 'totalTunai', 'totalKredit',
                'tanggalMulai', 'tanggalSelesai', 'tipePembayaran'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('laporan-penjualan-' . now()->format('Ymd') . '.pdf');
        }

        if ($export === 'excel') {
            return Excel::download(
                new PenjualanExport($tanggalMulai, $tanggalSelesai, $tipePembayaran),
                'laporan-penjualan-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('laporan.penjualan', compact(
            'penjualan', 'totalPenjualan', 'totalTunai', 'totalKredit',
            'tanggalMulai', 'tanggalSelesai', 'tipePembayaran'
        ));
    }

    public function piutang(Request $request): mixed
    {
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();

        $piutang = Penjualan::query()
            ->with(['pelanggan', 'user', 'pembayaranPiutang'])
            ->kredit()
            ->belumLunas()
            ->when($tanggalMulai !== '', fn ($q) => $q->whereDate('jatuh_tempo', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($q) => $q->whereDate('jatuh_tempo', '<=', $tanggalSelesai))
            ->latest('jatuh_tempo')
            ->latest('id')
            ->get();

        $totalPiutang = $piutang->sum('sisa_piutang');

        $export = $request->query('export');

        if ($export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.piutang', compact(
                'piutang', 'totalPiutang', 'tanggalMulai', 'tanggalSelesai'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('laporan-piutang-' . now()->format('Ymd') . '.pdf');
        }

        if ($export === 'excel') {
            return Excel::download(
                new PiutangExport($tanggalMulai, $tanggalSelesai),
                'laporan-piutang-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('laporan.piutang', compact(
            'piutang', 'totalPiutang', 'tanggalMulai', 'tanggalSelesai'
        ));
    }

    public function absensi(Request $request): mixed
    {
        $tanggalMulai = $request->string('tanggal_mulai')->trim()->toString();
        $tanggalSelesai = $request->string('tanggal_selesai')->trim()->toString();
        $pegawaiId = $request->integer('pegawai_id');

        $absensi = Absensi::query()
            ->with(['pegawai', 'user'])
            ->when($tanggalMulai !== '', fn ($q) => $q->whereDate('tanggal', '>=', $tanggalMulai))
            ->when($tanggalSelesai !== '', fn ($q) => $q->whereDate('tanggal', '<=', $tanggalSelesai))
            ->when($pegawaiId > 0, fn ($q) => $q->where('pegawai_id', $pegawaiId))
            ->latest('tanggal')
            ->latest('id')
            ->get();

        $pegawaiList = Pegawai::query()->orderBy('nama')->get();

        $summary = [
            'hadir' => $absensi->where('status', Absensi::STATUS_HADIR)->count(),
            'izin'  => $absensi->where('status', Absensi::STATUS_IZIN)->count(),
            'sakit' => $absensi->where('status', Absensi::STATUS_SAKIT)->count(),
            'alpha' => $absensi->where('status', Absensi::STATUS_ALPHA)->count(),
        ];

        $gajiSummary = $absensi->groupBy('pegawai_id')->map(function ($items) {
            $pegawai      = $items->first()->pegawai;
            $jumlahHadir  = $items->where('status', Absensi::STATUS_HADIR)->count();
            $gajiHarian   = $pegawai->gaji_harian ?? 0;
            return [
                'nama'         => $pegawai->nama ?? '-',
                'jabatan'      => $pegawai->jabatan ?? '-',
                'jumlah_hadir' => $jumlahHadir,
                'gaji_harian'  => $gajiHarian,
                'total_gaji'   => $jumlahHadir * $gajiHarian,
            ];
        })->values();

        $totalGaji = $gajiSummary->sum('total_gaji');

        $export = $request->query('export');

        if ($export === 'pdf') {
            $pdf = Pdf::loadView('laporan.pdf.absensi', compact(
                'absensi', 'summary', 'gajiSummary', 'totalGaji',
                'pegawaiList', 'tanggalMulai', 'tanggalSelesai', 'pegawaiId'
            ))->setPaper('a4', 'landscape');

            return $pdf->download('laporan-absensi-' . now()->format('Ymd') . '.pdf');
        }

        if ($export === 'excel') {
            return Excel::download(
                new AbsensiExport($tanggalMulai, $tanggalSelesai, $pegawaiId),
                'laporan-absensi-' . now()->format('Ymd') . '.xlsx'
            );
        }

        return view('laporan.absensi', compact(
            'absensi', 'summary', 'gajiSummary', 'totalGaji',
            'pegawaiList', 'tanggalMulai', 'tanggalSelesai', 'pegawaiId'
        ));
    }
}

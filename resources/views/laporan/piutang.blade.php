@extends('layouts.app')

@section('title', 'Laporan Piutang')

@section('content')
    <x-ui.page-header title="Laporan Piutang" description="Daftar penjualan kredit yang belum lunas, difilter berdasarkan jatuh tempo.">
        <a href="{{ route('laporan.piutang', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="file-spreadsheet" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.piutang', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        <a href="{{ route('laporan.stok') }}" class="btn {{ request()->routeIs('laporan.stok') ? 'btn-primary' : 'btn-secondary' }}">Stok</a>
        <a href="{{ route('laporan.pembelian') }}" class="btn {{ request()->routeIs('laporan.pembelian') ? 'btn-primary' : 'btn-secondary' }}">Pembelian</a>
        <a href="{{ route('laporan.penjualan') }}" class="btn {{ request()->routeIs('laporan.penjualan') ? 'btn-primary' : 'btn-secondary' }}">Penjualan</a>
        <a href="{{ route('laporan.piutang') }}" class="btn {{ request()->routeIs('laporan.piutang') ? 'btn-primary' : 'btn-secondary' }}">Piutang</a>
        <a href="{{ route('laporan.absensi') }}" class="btn {{ request()->routeIs('laporan.absensi') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
    </div>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-slate-500">Filter jatuh tempo:</span>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field w-auto">
                <span class="text-slate-400">s/d</span>
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field w-auto">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if ($tanggalMulai || $tanggalSelesai)
                    <a href="{{ route('laporan.piutang') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>

        @if ($piutang->isEmpty())
            <x-ui.empty-state title="Tidak ada piutang outstanding" description="Semua piutang sudah lunas, atau tidak ada data dalam rentang jatuh tempo yang dipilih." icon="wallet" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-rose-50/40 px-5 py-4">
                <div class="flex flex-wrap gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Debitur</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $piutang->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Piutang Tersisa</p>
                        <p class="mt-1 text-2xl font-bold text-rose-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Penjualan</th>
                            <th>Pelanggan</th>
                            <th>Tgl. Transaksi</th>
                            <th>Jatuh Tempo</th>
                            <th>Total</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Piutang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($piutang as $item)
                            @php
                                $jatuhTempo = $item->jatuh_tempo;
                                $lewatJatuhTempo = $jatuhTempo && $jatuhTempo->isPast();
                            @endphp
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</td>
                                <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="{{ $lewatJatuhTempo ? 'font-semibold text-rose-600' : '' }}">
                                    {{ optional($jatuhTempo)->translatedFormat('d M Y') ?? '-' }}
                                    @if ($lewatJatuhTempo)
                                        <span class="badge badge-danger ml-1">Lewat</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="text-emerald-700">Rp {{ number_format($item->dibayar, 0, ',', '.') }}</td>
                                <td class="font-bold text-rose-700">Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger' }}">
                                        {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="6" class="px-5 py-4 text-sm font-semibold text-slate-700">Total {{ $piutang->count() }} piutang belum lunas</td>
                            <td class="px-5 py-4 text-sm font-bold text-rose-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')

@section('content')
    <x-ui.page-header title="Laporan Mutasi Stok" description="Rekap seluruh pergerakan stok (masuk, keluar, penyesuaian) lintas barang.">
        <a href="{{ route('laporan.mutasi-stok', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="file-spreadsheet" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.mutasi-stok', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    @include('laporan.partials.nav')

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 sm:grid-cols-[180px_180px_170px_180px_auto]">
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <select name="tipe" class="select-field">
                    <option value="">Semua tipe</option>
                    <option value="masuk" @selected($tipe === 'masuk')>Masuk</option>
                    <option value="keluar" @selected($tipe === 'keluar')>Keluar</option>
                    <option value="penyesuaian" @selected($tipe === 'penyesuaian')>Penyesuaian</option>
                </select>
                <select name="sumber" class="select-field">
                    <option value="">Semua sumber</option>
                    <option value="pembelian" @selected($sumber === 'pembelian')>Stok Masuk</option>
                    <option value="penjualan" @selected($sumber === 'penjualan')>Stok Keluar</option>
                    <option value="retur_penjualan" @selected($sumber === 'retur_penjualan')>Retur Stok Keluar</option>
                    <option value="stock_opname" @selected($sumber === 'stock_opname')>Stok Opname</option>
                    <option value="manual" @selected($sumber === 'manual')>Manual</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($mutasi->isEmpty())
            <x-ui.empty-state title="Belum ada mutasi stok" description="Tidak ada pergerakan stok dalam periode atau filter yang dipilih." icon="arrow-right-left" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-slate-50/60 px-5 py-4">
                <div class="flex flex-wrap gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Mutasi</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $mutasi->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Masuk</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-700">+{{ number_format($totalMasuk) }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Keluar</p>
                        <p class="mt-1 text-2xl font-bold text-rose-700">-{{ number_format($totalKeluar) }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Barang</th>
                            <th>Tipe</th>
                            <th>Sumber</th>
                            <th class="!text-right">Jumlah</th>
                            <th class="!text-right">Stok</th>
                            <th>Keterangan</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mutasi as $item)
                            @php
                                $tipeBadge = match($item->tipe) {
                                    'masuk' => 'badge-success',
                                    'keluar' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap text-slate-500">{{ optional($item->created_at)->translatedFormat('d M Y H:i') }}</td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->barang->nama ?? '-' }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $item->barang->kode_barang ?? '' }}</div>
                                </td>
                                <td><span class="badge {{ $tipeBadge }}">{{ ucfirst($item->tipe) }}</span></td>
                                <td class="text-slate-600">{{ str_replace('_', ' ', ucfirst($item->sumber)) }}</td>
                                <td class="text-right font-semibold {{ $item->tipe === 'keluar' ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $item->tipe === 'keluar' ? '-' : '+' }}{{ number_format($item->jumlah) }}
                                </td>
                                <td class="whitespace-nowrap text-right text-slate-600">{{ $item->stok_sebelum }} → <span class="font-semibold text-slate-900">{{ $item->stok_sesudah }}</span></td>
                                <td class="max-w-[220px] truncate text-sm text-slate-500">{{ $item->keterangan ?: '-' }}</td>
                                <td class="text-slate-500">{{ $item->user->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Laporan Stok Keluar')

@section('content')
    <x-ui.page-header title="Laporan Stok Keluar" description="Rekap transaksi stok keluar dalam periode tertentu.">
        <a href="{{ route('laporan.stok-keluar', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="file-spreadsheet" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.stok-keluar', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    @include('laporan.partials.nav')

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 sm:grid-cols-[180px_180px_180px_auto]">
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <select name="tipe_pembayaran" class="select-field">
                    <option value="">Semua tipe</option>
                    <option value="tunai" @selected($tipePembayaran === 'tunai')>Tunai</option>
                    <option value="kredit" @selected($tipePembayaran === 'kredit')>Kredit</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($penjualan->isEmpty())
            <x-ui.empty-state title="Belum ada data stok keluar" description="Tidak ada transaksi stok keluar dalam periode yang dipilih." icon="shopping-cart" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-slate-50/60 px-5 py-4">
                <div class="flex flex-wrap gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Transaksi</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $penjualan->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Stok Keluar</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Tunai</p>
                        <p class="mt-1 text-2xl font-bold text-emerald-700">Rp {{ number_format($totalTunai, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Kredit</p>
                        <p class="mt-1 text-2xl font-bold text-amber-700">Rp {{ number_format($totalKredit, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Total</th>
                            <th>Dibayar</th>
                            <th>Sisa Piutang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</td>
                                <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $item->tipe_pembayaran === 'tunai' ? 'badge-success' : 'badge-warning' }}">
                                        {{ ucfirst($item->tipe_pembayaran) }}
                                    </span>
                                </td>
                                <td class="font-semibold text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->dibayar, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $item->status_pembayaran === 'lunas' ? 'badge-success' : ($item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="4" class="px-5 py-4 text-sm font-semibold text-slate-700">Total {{ $penjualan->count() }} transaksi</td>
                            <td class="px-5 py-4 text-sm font-bold text-slate-900">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>
@endsection

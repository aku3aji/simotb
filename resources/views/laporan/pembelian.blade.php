@extends('layouts.app')

@section('title', 'Laporan Stok Masuk')

@section('content')
    <x-ui.page-header title="Laporan Stok Masuk" description="Rekap transaksi stok masuk barang dari vendor dalam periode tertentu.">
        <a href="{{ route('laporan.stok-masuk', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="file-spreadsheet" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.stok-masuk', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    @include('laporan.partials.nav')

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 sm:grid-cols-[180px_180px_auto]">
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field" placeholder="Tanggal mulai">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field" placeholder="Tanggal selesai">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($pembelian->isEmpty())
            <x-ui.empty-state title="Belum ada data stok masuk" description="Tidak ada transaksi stok masuk dalam periode yang dipilih." icon="receipt" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-slate-50/60 px-5 py-4">
                <div class="flex flex-wrap gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Transaksi</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $pembelian->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Stok Masuk</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Vendor</th>
                            <th>Tanggal</th>
                            <th>Dicatat Oleh</th>
                            <th>Total</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_pembelian }}</td>
                                <td>{{ $item->vendor->nama ?? '-' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td class="font-semibold text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="text-slate-500">{{ $item->catatan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="4" class="px-5 py-4 text-sm font-semibold text-slate-700">Total {{ $pembelian->count() }} transaksi</td>
                            <td class="px-5 py-4 text-sm font-bold text-slate-900">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>
@endsection

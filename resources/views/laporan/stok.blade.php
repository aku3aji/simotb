@extends('layouts.app')

@section('title', 'Laporan Stok')

@section('content')
    <x-ui.page-header title="Laporan Stok Barang" description="Ringkasan kondisi stok seluruh barang saat ini.">
        <a href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="file-spreadsheet" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.stok', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    @include('laporan.partials.nav')

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
                <select name="kategori_id" class="select-field">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat->id }}" @selected($kategoriId === $kat->id)>{{ $kat->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($barang->isEmpty())
            <x-ui.empty-state title="Tidak ada data barang" description="Belum ada barang yang cocok dengan filter yang dipilih." icon="package" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Merek</th>
                            <th>Satuan</th>
                            <th>Stok</th>
                            <th>Stok Min.</th>
                            <th>Harga Jual</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barang as $item)
                            <tr>
                                <td class="font-mono text-xs">{{ $item->kode_barang }}</td>
                                <td class="font-semibold text-slate-900">{{ $item->nama }}</td>
                                <td>{{ $item->kategori->nama ?? '-' }}</td>
                                <td>{{ $item->merek->nama ?? '-' }}</td>
                                <td>{{ $item->satuan->nama ?? '-' }}</td>
                                <td class="font-semibold {{ $item->stok <= $item->stok_minimum ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ number_format($item->stok) }}
                                </td>
                                <td class="text-slate-500">{{ number_format($item->stok_minimum) }}</td>
                                <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    @if ($item->stok <= $item->stok_minimum)
                                        <span class="badge badge-danger">Menipis</span>
                                    @else
                                        <span class="badge badge-success">Aman</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="5" class="px-5 py-4 text-sm font-semibold text-slate-700">Total {{ $barang->count() }} barang</td>
                            <td class="px-5 py-4 text-sm font-bold text-slate-900">{{ number_format($barang->sum('stok')) }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>
@endsection

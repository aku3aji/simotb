@extends('layouts.app')

@section('title', 'Mutasi Stok')

@section('content')
    <x-ui.page-header title="Mutasi Stok" description="Daftar barang. Klik Riwayat untuk melihat riwayat keluar masuk stok per barang." />

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nama atau kode barang">
                </div>
                <select name="kategori_id" class="select-field">
                    <option value="">Semua kategori</option>
                    @foreach ($kategoriList as $kat)
                        <option value="{{ $kat->id }}" @selected($kategoriId == $kat->id)>{{ $kat->nama }}</option>
                    @endforeach
                </select>
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($barang->isEmpty())
            <x-ui.empty-state title="Belum ada barang" description="Tambahkan barang di menu Master Data terlebih dahulu." icon="package" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th class="!text-right">Stok Saat Ini</th>
                            <th class="!text-center">Total Mutasi</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barang as $item)
                            <tr>
                                <td class="font-mono text-xs text-slate-500">{{ $item->kode_barang }}</td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->nama }}</div>
                                    @if (!$item->is_active)
                                        <span class="badge badge-muted mt-0.5">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-slate-600">{{ $item->kategori->nama ?? '-' }}</td>
                                <td class="text-slate-600">{{ $item->satuan->nama ?? '-' }}</td>
                                <td class="text-right">
                                    <span class="{{ $item->stok <= $item->stok_minimum ? 'font-bold text-rose-700' : 'font-semibold text-slate-900' }}">
                                        {{ number_format($item->stok, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-muted">{{ number_format($item->stok_mutasi_count, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('inventory.mutasi-stok.show', $item) }}"
                                       class="btn btn-secondary px-3 py-2 text-xs">
                                        <x-ui.icon name="arrow-right-left" class="h-3.5 w-3.5" />
                                        <span>Riwayat</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $barang->links() }}
        @endif
    </section>
@endsection

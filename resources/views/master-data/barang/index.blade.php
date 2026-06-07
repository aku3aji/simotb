@extends('layouts.app')

@section('title', 'Barang')

@section('content')
    <x-ui.page-header title="Manajemen Barang" description="Kelola inventaris, harga jual, dan ketersediaan stok material.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('master-data.barang.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Tambah Barang Baru</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('master-data.barang.bulk-destroy') }}"
          data-confirm="Hapus semua barang yang dipilih? Tindakan ini tidak dapat dibatalkan.">
        @csrf
        @method('DELETE')
    </form>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari SKU, nama barang, kategori, atau merek...">
                </div>
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <div class="flex gap-3">
                    <button type="submit" class="btn btn-secondary">Cari</button>
                    @if ($q)
                        <a href="{{ route('master-data.barang.index') }}" class="btn btn-secondary">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        @if ($barang->isEmpty())
            <x-ui.empty-state title="Barang belum tersedia" description="Tambahkan barang pertama agar modul pembelian dan penjualan bisa dipakai." icon="package" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <x-ui.sortable-th column="nama" label="Nama Barang & SKU" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Kategori</th>
                            <th>Merek</th>
                            <th>Satuan</th>
                            <th>Harga Jual</th>
                            <x-ui.sortable-th column="stok" label="Stok" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Status</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barang as $item)
                            <tr>
                                <td class="!px-3"><input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->nama }}</div>
                                    <div class="mt-1 text-xs text-slate-500">SKU: {{ $item->kode_barang }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-primary">{{ $item->kategori->nama ?? '-' }}</span>
                                </td>
                                <td>{{ $item->merek->nama ?? '-' }}</td>
                                <td>{{ $item->satuan->nama ?? '-' }}</td>
                                <td class="font-semibold text-slate-900">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full {{ $item->stok <= 0 ? 'bg-rose-500' : ($item->stok <= $item->stok_minimum ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                        <span class="font-semibold text-slate-900">{{ $item->stok }}</span>
                                    </div>
                                    <div class="mt-1 text-xs text-slate-500">Min: {{ $item->stok_minimum }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ ! $item->is_active ? 'badge-muted' : ($item->stok <= 0 ? 'badge-danger' : ($item->stok <= $item->stok_minimum ? 'badge-warning' : 'badge-success')) }}">
                                        {{ ! $item->is_active ? 'Nonaktif' : ($item->stok <= 0 ? 'Habis' : ($item->stok <= $item->stok_minimum ? 'Menipis' : 'Aman')) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('master-data.barang.show', $item) }}" class="btn btn-secondary px-3 py-2" title="Lihat Detail">
                                            <x-ui.icon name="eye" class="h-4 w-4" />
                                        </a>
                                        <a href="{{ route('master-data.barang.edit', $item) }}" class="btn btn-secondary px-3 py-2" title="Edit">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('master-data.barang.destroy', $item) }}" data-confirm="Hapus barang '{{ $item->nama }}'?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-3 py-2" title="Hapus">
                                                <x-ui.icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
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

@extends('layouts.app')

@section('title', 'Stok Opname')

@section('content')
    <x-ui.page-header title="Stock Opname" description="Catat hasil pengecekan stok fisik dan sesuaikan dengan stok sistem.">
        <a href="{{ route('inventory.stok-opname.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Buat Opname Baru</span>
        </a>
    </x-ui.page-header>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nomor opname">
                </div>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field">
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($stokOpname->isEmpty())
            <x-ui.empty-state title="Belum ada stock opname" description="Buat opname baru untuk mencatat hasil perhitungan fisik di gudang atau toko." icon="boxes" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <x-ui.sortable-th column="nomor_opname" label="Nomor Opname" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-ui.sortable-th column="tanggal" label="Tanggal" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Jumlah Item</th>
                            <th>Dicatat Oleh</th>
                            <th>Catatan</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($stokOpname as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_opname }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->detail_count }} item</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>{{ $item->catatan ?: '-' }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('inventory.stok-opname.show', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="eye" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('inventory.stok-opname.destroy', $item) }}" data-confirm="Hapus stock opname '{{ $item->nomor_opname }}'?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-3 py-2">
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

            {{ $stokOpname->links() }}
        @endif
    </section>
@endsection

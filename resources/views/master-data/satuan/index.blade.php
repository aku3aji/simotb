@extends('layouts.app')

@section('title', 'Satuan')

@section('content')
    <x-ui.page-header title="Satuan Barang" description="Daftar satuan operasional seperti sak, dus, galon, atau batang.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('master-data.satuan.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Tambah Satuan</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('master-data.satuan.bulk-destroy') }}"
          data-confirm="Hapus semua satuan yang dipilih? Tindakan ini tidak dapat dibatalkan.">
        @csrf
        @method('DELETE')
    </form>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nama atau singkatan satuan">
                </div>
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($satuan->isEmpty())
            <x-ui.empty-state title="Satuan belum tersedia" description="Tambahkan satuan untuk melengkapi data barang." icon="boxes" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <x-ui.sortable-th column="nama" label="Nama" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-ui.sortable-th column="singkatan" label="Singkatan" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-ui.sortable-th column="created_at" label="Dibuat" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($satuan as $item)
                            <tr>
                                <td class="!px-3"><input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></td>
                                <td class="font-semibold text-slate-900">{{ $item->nama }}</td>
                                <td>{{ $item->singkatan ?: '-' }}</td>
                                <td>{{ optional($item->created_at)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('master-data.satuan.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('master-data.satuan.destroy', $item) }}" data-confirm="Hapus satuan '{{ $item->nama }}'?">
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

            {{ $satuan->links() }}
        @endif
    </section>
@endsection

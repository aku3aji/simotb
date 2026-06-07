@extends('layouts.app')

@section('title', 'Pegawai')

@section('content')
    <x-ui.page-header title="Data Pegawai" description="Kelola daftar pegawai dan status aktif mereka di toko.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('pegawai.pegawai.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Tambah Pegawai</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('pegawai.pegawai.bulk-destroy') }}"
          data-confirm="Hapus semua pegawai yang dipilih? Tindakan ini tidak dapat dibatalkan.">
        @csrf
        @method('DELETE')
    </form>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn {{ request()->routeIs('pegawai.pegawai.*') ? 'btn-primary' : 'btn-secondary' }}">Data Pegawai</a>
        <a href="{{ route('pegawai.absensi.index') }}" class="btn {{ request()->routeIs('pegawai.absensi.*') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
    </div>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_220px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nama, jabatan, atau telepon pegawai">
                </div>
                <select name="status" class="select-field">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($pegawai->isEmpty())
            <x-ui.empty-state title="Belum ada data pegawai" description="Tambahkan pegawai untuk mulai mencatat absensi dan menghubungkannya ke user." icon="users" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <x-ui.sortable-th column="nama" label="Nama Pegawai" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-ui.sortable-th column="jabatan" label="Jabatan" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Telepon</th>
                            <x-ui.sortable-th column="tanggal_masuk" label="Tanggal Masuk" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Status</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pegawai as $item)
                            <tr>
                                <td class="!px-3"><input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></td>
                                <td class="font-semibold text-slate-900">{{ $item->nama }}</td>
                                <td>{{ $item->jabatan ?: '-' }}</td>
                                <td>{{ $item->telepon ?: '-' }}</td>
                                <td>{{ optional($item->tanggal_masuk)->translatedFormat('d M Y') ?: '-' }}</td>
                                <td>
                                    <span class="badge {{ $item->status === 'aktif' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('pegawai.pegawai.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('pegawai.pegawai.destroy', $item) }}" data-confirm="Hapus pegawai '{{ $item->nama }}'?">
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

            {{ $pegawai->links() }}
        @endif
    </section>
@endsection

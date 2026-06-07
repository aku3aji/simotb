@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <x-ui.page-header title="Absensi Pegawai" description="Pantau kehadiran, izin, sakit, dan alpha pegawai harian.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('pegawai.absensi.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Input Absensi</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('pegawai.absensi.bulk-destroy') }}"
          data-confirm="Hapus semua absensi yang dipilih? Tindakan ini tidak dapat dibatalkan.">
        @csrf
        @method('DELETE')
    </form>

    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn {{ request()->routeIs('pegawai.pegawai.*') ? 'btn-primary' : 'btn-secondary' }}">Data Pegawai</a>
        <a href="{{ route('pegawai.absensi.index') }}" class="btn {{ request()->routeIs('pegawai.absensi.*') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
        <a href="{{ route('pegawai.absensi.catat-massal') }}" class="btn btn-secondary ml-auto">
            <x-ui.icon name="clipboard-list" class="h-4 w-4" />
            <span>Catat Massal</span>
        </a>
    </div>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_180px_180px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nama pegawai">
                </div>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="input-field">
                <select name="status" class="select-field">
                    <option value="">Semua status</option>
                    <option value="hadir" @selected($status === 'hadir')>Hadir</option>
                    <option value="izin" @selected($status === 'izin')>Izin</option>
                    <option value="sakit" @selected($status === 'sakit')>Sakit</option>
                    <option value="alpha" @selected($status === 'alpha')>Alpha</option>
                </select>
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($absensi->isEmpty())
            <x-ui.empty-state title="Data absensi belum tersedia" description="Catatan absensi akan muncul setelah Anda melakukan input kehadiran." icon="clipboard-list" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <th>Pegawai</th>
                            <x-ui.sortable-th column="tanggal" label="Tanggal" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Jam Kerja</th>
                            <x-ui.sortable-th column="status" label="Status" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Pencatat</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absensi as $item)
                            <tr>
                                <td class="!px-3"><input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->pegawai->nama ?? '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $item->pegawai->jabatan ?? '-' }}</div>
                                </td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->jam_masuk ?: '--:--' }} - {{ $item->jam_keluar ?: '--:--' }}</td>
                                <td>
                                    <span class="badge {{ $item->status === 'hadir' ? 'badge-success' : ($item->status === 'alpha' ? 'badge-danger' : 'badge-warning') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('pegawai.absensi.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('pegawai.absensi.destroy', $item) }}" data-confirm="Hapus absensi {{ $item->pegawai->nama ?? '' }} tanggal {{ optional($item->tanggal)->format('d/m/Y') }}?">
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

            {{ $absensi->links() }}
        @endif
    </section>
@endsection

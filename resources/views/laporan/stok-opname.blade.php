@extends('layouts.app')

@section('title', 'Laporan Stok Opname')

@section('content')
    <x-ui.page-header title="Laporan Stok Opname" description="Rekap sesi penyesuaian stok fisik (stock opname) dalam periode tertentu.">
        <a href="{{ route('laporan.stok-opname', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="file-spreadsheet" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.stok-opname', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    @include('laporan.partials.nav')

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 sm:grid-cols-[180px_180px_auto]">
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($opname->isEmpty())
            <x-ui.empty-state title="Belum ada stok opname" description="Tidak ada sesi stock opname dalam periode yang dipilih." icon="table" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-slate-50/60 px-5 py-4">
                <div class="flex flex-wrap gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Sesi</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $totalSesi }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Item Diperiksa</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($totalItem) }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor Opname</th>
                            <th>Tanggal</th>
                            <th class="!text-right">Jumlah Item</th>
                            <th class="!text-right">Total Selisih</th>
                            <th>Catatan</th>
                            <th>Dicatat Oleh</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($opname as $item)
                            @php $selisih = (int) ($item->detail_sum_selisih ?? 0); @endphp
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_opname }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="text-right">{{ number_format($item->detail_count) }}</td>
                                <td class="text-right font-semibold {{ $selisih > 0 ? 'text-emerald-700' : ($selisih < 0 ? 'text-rose-700' : 'text-slate-500') }}">
                                    {{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih) }}
                                </td>
                                <td class="max-w-[220px] truncate text-sm text-slate-500">{{ $item->catatan ?: '-' }}</td>
                                <td class="text-slate-500">{{ $item->user->name ?? '-' }}</td>
                                <td>
                                    <div class="flex justify-center">
                                        <a href="{{ route('inventory.stok-opname.show', $item) }}" class="btn btn-secondary px-3 py-2" title="Detail">
                                            <x-ui.icon name="eye" class="h-4 w-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

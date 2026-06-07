@extends('layouts.app')

@section('title', 'Detail Stock Opname')

@section('content')
    <x-ui.page-header title="Detail Stock Opname" description="{{ $stokOpname->nomor_opname }}">
        <a href="{{ route('inventory.stok-opname.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
        <aside class="space-y-6">
            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Informasi Opname</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Nomor Opname</dt>
                        <dd class="font-mono font-semibold text-slate-900">{{ $stokOpname->nomor_opname }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tanggal</dt>
                        <dd class="font-semibold text-slate-900">{{ optional($stokOpname->tanggal)->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Dicatat Oleh</dt>
                        <dd class="font-semibold text-slate-900">{{ $stokOpname->user->name ?? '-' }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Jumlah Item</dt>
                        <dd class="font-semibold text-slate-900">{{ $stokOpname->detail->count() }} item</dd>
                    </div>
                </dl>

                @if ($stokOpname->catatan)
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Catatan</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $stokOpname->catatan }}</p>
                    </div>
                @endif
            </section>

            @php
                $totalSelisihPlus  = $stokOpname->detail->where('selisih', '>', 0)->sum('selisih');
                $totalSelisihMinus = $stokOpname->detail->where('selisih', '<', 0)->sum('selisih');
                $itemBerselisih    = $stokOpname->detail->filter(fn ($d) => $d->selisih !== 0)->count();
            @endphp
            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Ringkasan Selisih</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Item Berselisih</dt>
                        <dd class="font-semibold {{ $itemBerselisih > 0 ? 'text-rose-700' : 'text-slate-900' }}">{{ $itemBerselisih }} item</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Selisih Lebih</dt>
                        <dd class="font-semibold text-emerald-700">+{{ $totalSelisihPlus }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Selisih Kurang</dt>
                        <dd class="font-semibold text-rose-700">{{ $totalSelisihMinus }}</dd>
                    </div>
                </dl>
            </section>
        </aside>

        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Detail Barang</h2>
                <p class="mt-1 text-sm text-slate-500">Hasil pengecekan stok fisik dibandingkan stok sistem.</p>
            </div>

            @if ($stokOpname->detail->isEmpty())
                <x-ui.empty-state title="Tidak ada detail" description="Tidak ada item yang tercatat dalam opname ini." icon="boxes" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="!text-right">Stok Sistem</th>
                                <th class="!text-right">Stok Fisik</th>
                                <th class="!text-right">Selisih</th>
                                <th>Alasan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stokOpname->detail as $row)
                                <tr>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $row->barang->nama ?? '-' }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">{{ $row->barang->kode_barang ?? '' }}</div>
                                    </td>
                                    <td class="text-right">{{ $row->stok_sistem }}</td>
                                    <td class="text-right">{{ $row->stok_fisik }}</td>
                                    <td class="text-right font-semibold {{ $row->selisih === 0 ? 'text-slate-500' : ($row->selisih > 0 ? 'text-emerald-700' : 'text-rose-700') }}">
                                        {{ $row->selisih > 0 ? '+' : '' }}{{ $row->selisih }}
                                    </td>
                                    <td>{{ $row->alasan ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection

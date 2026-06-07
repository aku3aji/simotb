@extends('layouts.app')

@section('title', 'Piutang')

@section('content')
    <x-ui.page-header title="Piutang" description="Daftar penjualan kredit yang belum lunas dan riwayat pembayaran.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('transaksi.pembayaran-piutang.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Catat Pembayaran</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('transaksi.pembayaran-piutang.bulk-destroy') }}"
          data-confirm="Hapus semua histori pembayaran yang dipilih? Sisa piutang akan otomatis diperbarui.">
        @csrf
        @method('DELETE')
    </form>

    {{-- Filter --}}
    <section class="surface overflow-hidden mb-6">
        <form method="GET" class="px-5 py-4">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_180px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nomor penjualan atau nama pelanggan">
                </div>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if ($q || $tanggalMulai || $tanggalSelesai)
                    <a href="{{ route('transaksi.pembayaran-piutang.index') }}" class="btn btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </section>

    {{-- Piutang Outstanding --}}
    <section class="surface overflow-hidden mb-6">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Piutang Outstanding</h2>
                <p class="mt-0.5 text-sm text-slate-500">Penjualan kredit yang belum lunas</p>
            </div>
        </div>

        @if ($piutangOutstanding->isEmpty())
            <x-ui.empty-state title="Tidak ada piutang outstanding" description="Semua penjualan kredit sudah lunas atau tidak ada data." icon="wallet" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-rose-50/40 px-5 py-4">
                <div class="flex flex-wrap gap-8">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jumlah Debitur</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $piutangOutstanding->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Piutang Tersisa</p>
                        <p class="mt-1 text-2xl font-bold text-rose-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No. Penjualan</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Jatuh Tempo</th>
                            <th>Total</th>
                            <th>Sudah Dibayar</th>
                            <th>Sisa Piutang</th>
                            <th>Status</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($piutangOutstanding as $item)
                            @php
                                $jatuhTempo = $item->jatuh_tempo;
                                $lewat = $jatuhTempo && $jatuhTempo->isPast();
                            @endphp
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</td>
                                <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="{{ $lewat ? 'font-semibold text-rose-600' : '' }}">
                                    {{ optional($jatuhTempo)->translatedFormat('d M Y') ?? '-' }}
                                    @if ($lewat)
                                        <span class="badge badge-danger ml-1">Lewat</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td class="text-emerald-700">Rp {{ number_format($item->dibayar, 0, ',', '.') }}</td>
                                <td class="font-bold text-rose-700">Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger' }}">
                                        {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-center">
                                        <a href="{{ route('transaksi.pembayaran-piutang.create', ['penjualan_id' => $item->id]) }}"
                                           class="btn btn-primary px-3 py-2 text-xs">
                                            <x-ui.icon name="plus" class="h-3.5 w-3.5" />
                                            <span>Bayar</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td colspan="6" class="px-5 py-4 text-sm font-semibold text-slate-700">Total {{ $piutangOutstanding->count() }} piutang</td>
                            <td class="px-5 py-4 text-sm font-bold text-rose-700">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>

    {{-- Histori Pembayaran --}}
    <section class="surface overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-bold text-slate-900">Histori Pembayaran</h2>
            <p class="mt-0.5 text-sm text-slate-500">Catatan cicilan dan pelunasan piutang</p>
        </div>

        @if ($pembayaranPiutang->isEmpty())
            <x-ui.empty-state title="Belum ada histori pembayaran" description="Pembayaran piutang akan muncul di sini setelah dicatat." icon="receipt" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <th>No. Penjualan</th>
                            <th>Pelanggan</th>
                            <th>Tanggal Bayar</th>
                            <th>Jumlah Bayar</th>
                            <th>Metode</th>
                            <th>Dicatat Oleh</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembayaranPiutang as $item)
                            <tr>
                                <td class="!px-3"><input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></td>
                                <td class="font-semibold text-slate-900">{{ $item->penjualan->nomor_penjualan ?? '-' }}</td>
                                <td>{{ $item->penjualan->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="font-semibold text-emerald-700">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                                <td>{{ $item->metode_pembayaran ?: '-' }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('transaksi.pembayaran-piutang.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('transaksi.pembayaran-piutang.destroy', $item) }}" data-confirm="Hapus pembayaran Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }} ini?">
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

            {{ $pembayaranPiutang->links() }}
        @endif
    </section>
@endsection

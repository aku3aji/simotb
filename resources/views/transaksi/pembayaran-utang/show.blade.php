@extends('layouts.app')

@section('title', 'Riwayat Pembayaran — ' . $pembelian->nomor_pembelian)

@section('content')
    <x-ui.page-header
        title="Riwayat Pembayaran Utang"
        description="Detail cicilan dan pelunasan untuk transaksi {{ $pembelian->nomor_pembelian }}">
        <a href="{{ route('transaksi.pembayaran-utang.index') }}" class="btn btn-secondary">
            <x-ui.icon name="chevron-left" class="h-4 w-4" />
            <span>Kembali</span>
        </a>
        @if ($pembelian->sisa_utang > 0)
            <a href="{{ route('transaksi.pembayaran-utang.create', ['pembelian_id' => $pembelian->id]) }}"
               class="btn btn-primary">
                <x-ui.icon name="plus" class="h-4 w-4" />
                <span>Catat Pembayaran</span>
            </a>
        @endif
    </x-ui.page-header>

    {{-- Info Transaksi --}}
    <section class="surface mb-6 p-5">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Vendor</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $pembelian->vendor->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tanggal Transaksi</p>
                <p class="mt-1 font-semibold text-slate-900">{{ optional($pembelian->tanggal)->translatedFormat('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Jatuh Tempo</p>
                @php $lewat = $pembelian->jatuh_tempo && $pembelian->jatuh_tempo->isPast() && $pembelian->status_pembayaran !== 'lunas'; @endphp
                <p class="mt-1 font-semibold {{ $lewat ? 'text-rose-700' : 'text-slate-900' }}">
                    {{ optional($pembelian->jatuh_tempo)->translatedFormat('d M Y') ?? '-' }}
                    @if ($lewat) <span class="badge badge-danger ml-1">Lewat</span> @endif
                </p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</p>
                <p class="mt-1">
                    @php
                        $statusClass = match($pembelian->status_pembayaran) {
                            'lunas' => 'badge-success',
                            'sebagian' => 'badge-warning',
                            default => 'badge-danger',
                        };
                        $statusLabel = match($pembelian->status_pembayaran) {
                            'lunas' => 'Lunas',
                            'sebagian' => 'Sebagian',
                            default => 'Belum Lunas',
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                </p>
            </div>
        </div>

        <div class="mt-5 grid gap-4 border-t border-slate-200 pt-5 sm:grid-cols-3">
            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Tagihan</p>
                <p class="mt-1 text-xl font-bold text-slate-900">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Sudah Dibayar</p>
                <p class="mt-1 text-xl font-bold text-emerald-700">Rp {{ number_format($pembelian->dibayar, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Sisa Utang</p>
                <p class="mt-1 text-xl font-bold text-rose-700">Rp {{ number_format($pembelian->sisa_utang, 0, ',', '.') }}</p>
            </div>
        </div>
    </section>

    {{-- Riwayat Pembayaran --}}
    <section class="surface overflow-hidden">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-base font-bold text-slate-900">Riwayat Pembayaran</h2>
            <p class="mt-0.5 text-sm text-slate-500">{{ $pembelian->pembayaranUtang->count() }} catatan pembayaran</p>
        </div>

        @if ($pembelian->pembayaranUtang->isEmpty())
            <x-ui.empty-state title="Belum ada pembayaran" description="Belum ada cicilan atau pelunasan yang dicatat untuk transaksi ini." icon="receipt" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <th class="!text-right">Jumlah Bayar</th>
                            <th>Metode</th>
                            <th>Catatan</th>
                            <th>Dicatat Oleh</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian->pembayaranUtang->sortByDesc('tanggal') as $bayar)
                            <tr>
                                <td class="whitespace-nowrap text-slate-500">{{ optional($bayar->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="text-right font-semibold text-emerald-700">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="text-slate-600">{{ $bayar->metode_pembayaran ?: '-' }}</td>
                                <td class="max-w-[200px] truncate text-sm text-slate-500">{{ $bayar->catatan ?: '-' }}</td>
                                <td class="text-slate-500">{{ $bayar->user->name ?? '-' }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('transaksi.pembayaran-utang.edit', $bayar) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('transaksi.pembayaran-utang.destroy', $bayar) }}"
                                              data-confirm="Hapus pembayaran Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }} ini?">
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
                    <tfoot>
                        <tr class="bg-slate-50">
                            <td class="px-5 py-4 text-sm font-semibold text-slate-700">Total</td>
                            <td class="px-5 py-4 text-right text-sm font-bold text-emerald-700">
                                Rp {{ number_format($pembelian->pembayaranUtang->sum('jumlah_bayar'), 0, ',', '.') }}
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </section>
@endsection

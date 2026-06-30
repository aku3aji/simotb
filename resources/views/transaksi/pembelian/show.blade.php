@extends('layouts.app')

@section('title', 'Detail Stok Masuk')

@section('content')
    <x-ui.page-header title="Detail Stok Masuk" description="{{ $pembelian->nomor_pembelian }}">
        <div class="flex gap-3">
            @if ($pembelian->pembayaranUtang->isEmpty())
                <a href="{{ route('transaksi.stok-masuk.edit', $pembelian) }}" class="btn btn-secondary">
                    <x-ui.icon name="pencil" class="h-4 w-4" />
                    <span>Edit</span>
                </a>
            @endif
            @if ($pembelian->tipe_pembayaran === 'kredit' && $pembelian->sisa_utang > 0)
                <a href="{{ route('transaksi.pembayaran-utang.create', ['pembelian_id' => $pembelian->id]) }}" class="btn btn-primary">
                    <x-ui.icon name="wallet" class="h-4 w-4" />
                    <span>Bayar Utang</span>
                </a>
            @endif
            <a href="{{ route('transaksi.stok-masuk.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
        {{-- LEFT ASIDE --}}
        <aside class="space-y-6">
            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Informasi Transaksi</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Nomor Stok Masuk</dt>
                        <dd class="font-mono font-semibold text-slate-900">{{ $pembelian->nomor_pembelian }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tanggal</dt>
                        <dd class="font-semibold text-slate-900">{{ optional($pembelian->tanggal)->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Vendor</dt>
                        <dd class="font-semibold text-slate-900">{{ $pembelian->vendor->nama ?? '-' }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tipe Pembayaran</dt>
                        <dd>
                            <span class="badge {{ $pembelian->tipe_pembayaran === 'tunai' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($pembelian->tipe_pembayaran) }}
                            </span>
                        </dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Status</dt>
                        <dd>
                            <span class="badge {{ $pembelian->status_pembayaran === 'lunas' ? 'badge-success' : ($pembelian->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                {{ str_replace('_', ' ', ucfirst($pembelian->status_pembayaran)) }}
                            </span>
                        </dd>
                    </div>
                    @if ($pembelian->jatuh_tempo)
                        <div class="summary-item">
                            <dt class="text-slate-500">Jatuh Tempo</dt>
                            <dd class="font-semibold text-slate-900">{{ optional($pembelian->jatuh_tempo)->translatedFormat('d M Y') }}</dd>
                        </div>
                    @endif
                    <div class="summary-item">
                        <dt class="text-slate-500">Dicatat Oleh</dt>
                        <dd class="font-semibold text-slate-900">{{ $pembelian->user->name ?? '-' }}</dd>
                    </div>
                </dl>
                @if ($pembelian->catatan)
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Catatan</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $pembelian->catatan }}</p>
                    </div>
                @endif
            </section>

            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Ringkasan Pembayaran</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Total Stok Masuk</dt>
                        <dd class="font-semibold text-slate-900">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Sudah Dibayar</dt>
                        <dd class="font-semibold text-emerald-700">Rp {{ number_format($pembelian->dibayar, 0, ',', '.') }}</dd>
                    </div>
                    <div class="summary-item border-t border-slate-200 pt-3">
                        <dt class="font-semibold text-slate-700">Sisa Utang</dt>
                        <dd class="font-bold {{ $pembelian->sisa_utang > 0 ? 'text-rose-700' : 'text-slate-900' }}">
                            Rp {{ number_format($pembelian->sisa_utang, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </section>

            @if ($pembelian->pembayaranUtang->isNotEmpty())
                <section class="surface overflow-hidden">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-base font-bold text-slate-900">Riwayat Pembayaran</h2>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($pembelian->pembayaranUtang as $bayar)
                            <li class="px-5 py-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">{{ optional($bayar->tanggal)->translatedFormat('d M Y') }}</span>
                                    <span class="font-semibold text-emerald-700">+ Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</span>
                                </div>
                                @if ($bayar->metode_pembayaran)
                                    <p class="mt-0.5 text-xs text-slate-400">{{ ucfirst($bayar->metode_pembayaran) }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>

        {{-- RIGHT: DETAIL ITEMS --}}
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Item Stok Masuk</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $pembelian->detail->count() }} item</p>
            </div>

            @if ($pembelian->detail->isEmpty())
                <x-ui.empty-state title="Tidak ada item" description="Tidak ada item stok masuk yang tercatat." icon="receipt" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="!text-right">Jumlah</th>
                                <th class="!text-right">Harga Beli</th>
                                <th class="!text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelian->detail as $row)
                                <tr>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $row->barang->nama ?? '-' }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">{{ $row->barang->kode_barang ?? '' }}</div>
                                    </td>
                                    <td class="text-right">{{ $row->jumlah }}</td>
                                    <td class="text-right">Rp {{ number_format($row->harga_beli, 0, ',', '.') }}</td>
                                    <td class="text-right font-semibold text-slate-900">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-300">
                                <td colspan="3" class="text-right font-semibold text-slate-600">Total Stok Masuk</td>
                                <td class="text-right font-bold text-slate-900">Rp {{ number_format($pembelian->total, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection

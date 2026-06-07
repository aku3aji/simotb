@extends('layouts.app')

@section('title', 'Detail Penjualan')

@section('content')
    <x-ui.page-header title="Detail Penjualan" description="{{ $penjualan->nomor_penjualan }}">
        <div class="flex gap-3">
            <a href="{{ route('transaksi.penjualan.edit', $penjualan) }}" class="btn btn-secondary">
                <x-ui.icon name="pencil" class="h-4 w-4" />
                <span>Edit</span>
            </a>
            <a href="{{ route('transaksi.penjualan.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
        {{-- LEFT ASIDE --}}
        <aside class="space-y-6">
            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Informasi Transaksi</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Nomor Penjualan</dt>
                        <dd class="font-mono font-semibold text-slate-900">{{ $penjualan->nomor_penjualan }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tanggal</dt>
                        <dd class="font-semibold text-slate-900">{{ optional($penjualan->tanggal)->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Pelanggan</dt>
                        <dd class="font-semibold text-slate-900">{{ $penjualan->pelanggan->nama ?? 'Pelanggan Umum' }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tipe Pembayaran</dt>
                        <dd>
                            <span class="badge {{ $penjualan->tipe_pembayaran === 'tunai' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($penjualan->tipe_pembayaran) }}
                            </span>
                        </dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Status</dt>
                        <dd>
                            <span class="badge {{ $penjualan->status_pembayaran === 'lunas' ? 'badge-success' : ($penjualan->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                {{ str_replace('_', ' ', ucfirst($penjualan->status_pembayaran)) }}
                            </span>
                        </dd>
                    </div>
                    @if ($penjualan->jatuh_tempo)
                        <div class="summary-item">
                            <dt class="text-slate-500">Jatuh Tempo</dt>
                            <dd class="font-semibold text-slate-900">{{ optional($penjualan->jatuh_tempo)->translatedFormat('d M Y') }}</dd>
                        </div>
                    @endif
                    <div class="summary-item">
                        <dt class="text-slate-500">Dicatat Oleh</dt>
                        <dd class="font-semibold text-slate-900">{{ $penjualan->user->name ?? '-' }}</dd>
                    </div>
                </dl>
                @if ($penjualan->catatan)
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Catatan</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $penjualan->catatan }}</p>
                    </div>
                @endif
            </section>

            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Ringkasan Tagihan</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Total Penjualan</dt>
                        <dd class="font-semibold text-slate-900">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</dd>
                    </div>
                    @if ($totalRetur > 0)
                        <div class="summary-item">
                            <dt class="text-slate-500">Total Retur</dt>
                            <dd class="font-semibold text-rose-700">- Rp {{ number_format($totalRetur, 0, ',', '.') }}</dd>
                        </div>
                        <div class="summary-item border-t border-slate-200 pt-3">
                            <dt class="font-semibold text-slate-700">Total Efektif</dt>
                            <dd class="font-bold text-slate-900">Rp {{ number_format($effectiveTotal, 0, ',', '.') }}</dd>
                        </div>
                    @endif
                    <div class="summary-item">
                        <dt class="text-slate-500">Sudah Dibayar</dt>
                        <dd class="font-semibold text-emerald-700">Rp {{ number_format($penjualan->dibayar, 0, ',', '.') }}</dd>
                    </div>
                    <div class="summary-item border-t border-slate-200 pt-3">
                        <dt class="font-semibold text-slate-700">Sisa Piutang</dt>
                        <dd class="font-bold {{ $penjualan->sisa_piutang > 0 ? 'text-rose-700' : 'text-slate-900' }}">
                            Rp {{ number_format($penjualan->sisa_piutang, 0, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </section>

            @if ($penjualan->pembayaranPiutang->isNotEmpty())
                <section class="surface overflow-hidden">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-base font-bold text-slate-900">Riwayat Pembayaran</h2>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($penjualan->pembayaranPiutang as $bayar)
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

            @if ($penjualan->returPenjualan->isNotEmpty())
                <section class="surface overflow-hidden">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="text-base font-bold text-slate-900">Riwayat Retur</h2>
                    </div>
                    <ul class="divide-y divide-slate-100">
                        @foreach ($penjualan->returPenjualan as $retur)
                            <li class="px-5 py-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <a href="{{ route('transaksi.retur-penjualan.show', $retur) }}" class="font-mono font-semibold text-brand-700 hover:underline">{{ $retur->nomor_retur }}</a>
                                    <span class="font-semibold text-rose-700">- Rp {{ number_format($retur->total_retur, 0, ',', '.') }}</span>
                                </div>
                                <p class="mt-0.5 text-xs text-slate-400">{{ optional($retur->tanggal)->translatedFormat('d M Y') }}</p>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </aside>

        {{-- RIGHT: DETAIL ITEMS --}}
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Item Penjualan</h2>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $penjualan->detail->count() }} item
                    @if ($totalRetur > 0)
                        — <span class="text-rose-600 font-medium">ada {{ $penjualan->returPenjualan->count() }} retur</span>
                    @endif
                </p>
            </div>

            @if ($penjualan->detail->isEmpty())
                <x-ui.empty-state title="Tidak ada item" description="Tidak ada item penjualan yang tercatat." icon="shopping-cart" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="!text-right">Qty Jual</th>
                                <th class="!text-right">Qty Retur</th>
                                <th class="!text-right">Qty Efektif</th>
                                <th class="!text-right">Harga Jual</th>
                                <th class="!text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan->detail as $row)
                                @php
                                    $jumlahRetur = (int) ($returByBarang[$row->barang_id] ?? 0);
                                    $jumlahEfektif = max(0, (int) $row->jumlah - $jumlahRetur);
                                @endphp
                                <tr class="{{ $jumlahRetur > 0 ? 'bg-rose-50/40' : '' }}">
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $row->barang->nama ?? '-' }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">{{ $row->barang->kode_barang ?? '' }}</div>
                                        @if ($jumlahRetur > 0)
                                            <span class="badge badge-danger mt-1">Sebagian Diretur</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $row->jumlah }}</td>
                                    <td class="text-right {{ $jumlahRetur > 0 ? 'font-semibold text-rose-700' : 'text-slate-400' }}">
                                        {{ $jumlahRetur > 0 ? '-' . $jumlahRetur : '-' }}
                                    </td>
                                    <td class="text-right font-semibold {{ $jumlahEfektif === 0 ? 'text-slate-400 line-through' : 'text-slate-900' }}">
                                        {{ $jumlahEfektif }}
                                    </td>
                                    <td class="text-right">Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                                    <td class="text-right font-semibold text-slate-900">
                                        Rp {{ number_format($row->subtotal, 0, ',', '.') }}
                                        @if ($jumlahRetur > 0)
                                            <br><span class="text-xs text-rose-600">(efektif: Rp {{ number_format($jumlahEfektif * $row->harga_jual, 0, ',', '.') }})</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-300">
                                <td colspan="5" class="text-right font-semibold text-slate-600">Total Penjualan</td>
                                <td class="text-right font-bold text-slate-900">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            </tr>
                            @if ($totalRetur > 0)
                                <tr>
                                    <td colspan="5" class="text-right text-sm font-semibold text-rose-600">Total Retur</td>
                                    <td class="text-right text-sm font-semibold text-rose-600">- Rp {{ number_format($totalRetur, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-right font-bold text-slate-800">Total Efektif</td>
                                    <td class="text-right text-lg font-extrabold text-brand-800">Rp {{ number_format($effectiveTotal, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection

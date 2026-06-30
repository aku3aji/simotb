@extends('layouts.app')

@section('title', 'Detail Retur Stok Keluar')

@section('content')
    <x-ui.page-header title="Detail Retur Stok Keluar" description="{{ $returPenjualan->nomor_retur }}">
        <div class="flex gap-3">
            <a href="{{ route('transaksi.retur-stok-keluar.edit', $returPenjualan) }}" class="btn btn-secondary">
                <x-ui.icon name="pencil" class="h-4 w-4" />
                <span>Edit</span>
            </a>
            <a href="{{ route('transaksi.retur-stok-keluar.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
        <aside class="space-y-6">
            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Informasi Retur</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Nomor Retur</dt>
                        <dd class="font-mono font-semibold text-slate-900">{{ $returPenjualan->nomor_retur }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tanggal Retur</dt>
                        <dd class="font-semibold text-slate-900">{{ optional($returPenjualan->tanggal)->translatedFormat('d M Y') }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Dicatat Oleh</dt>
                        <dd class="font-semibold text-slate-900">{{ $returPenjualan->user->name ?? '-' }}</dd>
                    </div>
                </dl>

                @if ($returPenjualan->alasan)
                    <div class="mt-4 border-t border-slate-200 pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alasan Retur</p>
                        <p class="mt-2 text-sm text-slate-600">{{ $returPenjualan->alasan }}</p>
                    </div>
                @endif
            </section>

            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Transaksi Asal</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="summary-item">
                        <dt class="text-slate-500">Nomor Stok Keluar</dt>
                        <dd class="font-mono font-semibold text-slate-900">{{ $returPenjualan->penjualan->nomor_penjualan ?? '-' }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Pelanggan</dt>
                        <dd class="font-semibold text-slate-900">{{ $returPenjualan->penjualan->pelanggan->nama ?? 'Pelanggan Umum' }}</dd>
                    </div>
                    <div class="summary-item">
                        <dt class="text-slate-500">Tanggal Jual</dt>
                        <dd class="font-semibold text-slate-900">{{ optional($returPenjualan->penjualan?->tanggal)->translatedFormat('d M Y') ?? '-' }}</dd>
                    </div>
                </dl>
                <div class="mt-4 border-t border-slate-200 pt-4">
                    <a href="{{ route('transaksi.stok-keluar.edit', $returPenjualan->penjualan) }}" class="text-sm font-medium text-brand-700 hover:underline">
                        Lihat transaksi stok keluar →
                    </a>
                </div>
            </section>

            <section class="surface p-6">
                <h2 class="text-lg font-bold text-slate-900">Total Retur</h2>
                <p class="mt-3 text-4xl font-extrabold text-brand-800">
                    Rp {{ number_format($returPenjualan->total_retur, 0, ',', '.') }}
                </p>
            </section>
        </aside>

        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-lg font-bold text-slate-900">Detail Barang Retur</h2>
                <p class="mt-1 text-sm text-slate-500">Daftar barang yang dikembalikan beserta kondisinya.</p>
            </div>

            @if ($returPenjualan->detail->isEmpty())
                <x-ui.empty-state title="Tidak ada detail" description="Tidak ada item yang tercatat dalam retur ini." icon="rotate-ccw" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th class="!text-right">Jumlah</th>
                                <th class="!text-right">Harga Jual</th>
                                <th>Kondisi</th>
                                <th class="!text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returPenjualan->detail as $row)
                                <tr>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $row->barang->nama ?? '-' }}</div>
                                        <div class="mt-0.5 text-xs text-slate-500">{{ $row->barang->kode_barang ?? '' }}</div>
                                    </td>
                                    <td class="text-right">{{ $row->jumlah }}</td>
                                    <td class="text-right">Rp {{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $row->kondisi_barang === 'baik' ? 'badge-success' : 'badge-danger' }}">
                                            {{ ucfirst($row->kondisi_barang) }}
                                        </span>
                                    </td>
                                    <td class="text-right font-semibold text-slate-900">Rp {{ number_format($row->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-slate-300">
                                <td colspan="4" class="text-right font-semibold text-slate-600">Total Retur</td>
                                <td class="text-right text-lg font-extrabold text-brand-800">Rp {{ number_format($returPenjualan->total_retur, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection

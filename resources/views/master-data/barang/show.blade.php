@extends('layouts.app')

@section('title', 'Detail Barang')

@section('content')
    <x-ui.page-header title="Detail Barang" description="Informasi lengkap barang beserta riwayat harga pembelian dari vendor.">
        <a href="{{ route('master-data.barang.edit', $barang) }}" class="btn btn-secondary">
            <x-ui.icon name="pencil" class="h-4 w-4" />
            <span>Edit</span>
        </a>
        <a href="{{ route('master-data.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(0,1.6fr)]">

        {{-- Info Barang --}}
        <section class="surface divide-y divide-slate-100">
            <div class="px-6 py-4">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $barang->nama }}</h2>
                        <p class="mt-1 text-sm text-slate-500">SKU: {{ $barang->kode_barang }}</p>
                    </div>
                    <span class="badge {{ ! $barang->is_active ? 'badge-muted' : ($barang->stok <= 0 ? 'badge-danger' : ($barang->stok <= $barang->stok_minimum ? 'badge-warning' : 'badge-success')) }} shrink-0">
                        {{ ! $barang->is_active ? 'Nonaktif' : ($barang->stok <= 0 ? 'Habis' : ($barang->stok <= $barang->stok_minimum ? 'Menipis' : 'Aman')) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x divide-slate-100">
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Harga Beli</p>
                    <p class="mt-1 text-lg font-bold text-slate-900">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</p>
                </div>
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Harga Jual</p>
                    <p class="mt-1 text-lg font-bold text-brand-700">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x divide-slate-100">
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Stok Saat Ini</p>
                    <div class="mt-1 flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full {{ $barang->stok <= 0 ? 'bg-rose-500' : ($barang->stok <= $barang->stok_minimum ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                        <span class="text-lg font-bold text-slate-900">{{ $barang->stok }}</span>
                        <span class="text-sm text-slate-500">{{ $barang->satuan->nama ?? '' }}</span>
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Stok minimum: {{ $barang->stok_minimum }}</p>
                </div>
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Satuan</p>
                    <p class="mt-1 text-base font-semibold text-slate-900">{{ $barang->satuan->nama ?? '-' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 divide-x divide-slate-100">
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kategori</p>
                    <p class="mt-1">
                        @if ($barang->kategori)
                            <span class="badge badge-primary">{{ $barang->kategori->nama }}</span>
                        @else
                            <span class="text-slate-400">-</span>
                        @endif
                    </p>
                </div>
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Merek</p>
                    <p class="mt-1 text-base font-semibold text-slate-900">{{ $barang->merek->nama ?? '-' }}</p>
                </div>
            </div>

            @if ($barang->deskripsi)
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Deskripsi</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $barang->deskripsi }}</p>
                </div>
            @endif
        </section>

        {{-- Riwayat Harga Pembelian --}}
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900">Riwayat Harga Pembelian</h3>
                <p class="mt-1 text-sm text-slate-500">Perbandingan harga beli dari setiap vendor yang pernah menyuplai barang ini.</p>
            </div>

            @if ($riwayatHarga->isEmpty())
                <x-ui.empty-state title="Belum ada riwayat pembelian" description="Barang ini belum pernah masuk melalui transaksi pembelian." icon="receipt" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Pembelian</th>
                                <th>Vendor</th>
                                <th class="!text-right">Jumlah</th>
                                <th class="!text-right">Harga Beli</th>
                                <th class="!text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($riwayatHarga as $detail)
                                <tr>
                                    <td class="text-slate-600">
                                        {{ $detail->pembelian->tanggal->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        <span class="font-mono text-sm">{{ $detail->pembelian->nomor_pembelian }}</span>
                                    </td>
                                    <td>
                                        <span class="font-semibold text-slate-900">{{ $detail->pembelian->vendor->nama ?? '-' }}</span>
                                    </td>
                                    <td class="text-right">
                                        {{ $detail->jumlah }} {{ $barang->satuan->singkatan ?? $barang->satuan->nama ?? '' }}
                                    </td>
                                    <td class="text-right font-semibold text-slate-900">
                                        Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}
                                    </td>
                                    <td class="text-right font-semibold text-brand-700">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    </div>
@endsection

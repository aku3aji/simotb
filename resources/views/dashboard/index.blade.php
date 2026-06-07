@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-ui.page-header title="Ringkasan Operasional" description="Pantau performa inventaris, transaksi, dan piutang toko secara real-time.">
        <div class="rounded-md border border-slate-200 bg-white px-4 py-3 text-sm text-slate-500">
            Data terakhir diperbarui: {{ now()->translatedFormat('d F Y, H:i') }}
        </div>
    </x-ui.page-header>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-ui.stat-card
            title="Total Penjualan Bulan Ini"
            :value="'Rp ' . number_format($totalPenjualan, 0, ',', '.')"
            icon="shopping-cart"
            variant="success"
        />
        <x-ui.stat-card
            title="Total Pembelian Bulan Ini"
            :value="'Rp ' . number_format($totalPembelian, 0, ',', '.')"
            icon="receipt"
            variant="brand"
        />
        <x-ui.stat-card
            title="Stok Menipis"
            :value="$stokMenipis . ' item'"
            icon="alert-triangle"
            variant="danger"
            hint="Perlu cek"
        />
        <x-ui.stat-card
            title="Total Piutang"
            :value="'Rp ' . number_format($totalPiutang, 0, ',', '.')"
            icon="wallet"
            variant="warning"
        />
    </div>

    <div class="mt-6 grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(360px,0.9fr)]">
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-5">
                <h2 class="text-2xl font-extrabold text-slate-900">Aktivitas Transaksi Terbaru</h2>
                <p class="mt-1 text-sm text-slate-500">Lima transaksi terakhir yang masuk ke sistem.</p>
            </div>

            @if ($penjualanTerbaru->isEmpty())
                <x-ui.empty-state title="Belum ada transaksi penjualan" description="Transaksi terbaru akan muncul di sini setelah admin mulai mencatat penjualan." icon="shopping-cart" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nomor</th>
                                <th>Pelanggan</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualanTerbaru as $item)
                                <tr>
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</div>
                                        <div class="mt-1 text-xs text-slate-500">Dicatat oleh {{ $item->user->name ?? '-' }}</div>
                                    </td>
                                    <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                    <td class="font-semibold text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge {{ $item->status_pembayaran === 'lunas' ? 'badge-success' : ($item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                            {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="space-y-6">
            <div class="surface p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-extrabold text-slate-900">Peringatan Stok</h2>
                        <p class="mt-1 text-sm text-slate-500">Barang yang mendekati atau sudah melewati batas minimum.</p>
                    </div>
                    <span class="badge badge-danger">{{ $barangMenipis->count() }} item</span>
                </div>

                <div class="mt-5 space-y-4">
                    @forelse ($barangMenipis as $item)
                        <div class="rounded-lg border border-slate-200 px-4 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $item->nama }}</p>
                                    <p class="mt-1 text-sm text-slate-500">{{ $item->kode_barang }} • {{ $item->kategori->nama ?? '-' }} • {{ $item->merek->nama ?? 'Tanpa merek' }}</p>
                                </div>
                                <span class="badge {{ $item->stok <= 0 ? 'badge-danger' : 'badge-warning' }}">
                                    {{ $item->stok <= 0 ? 'Habis' : 'Menipis' }}
                                </span>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Stok saat ini</span>
                                <span class="font-bold text-slate-900">{{ $item->stok }} {{ $item->satuan->singkatan ?? $item->satuan->nama ?? '' }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between text-sm">
                                <span class="text-slate-500">Batas minimum</span>
                                <span class="font-semibold text-slate-700">{{ $item->stok_minimum }}</span>
                            </div>
                        </div>
                    @empty
                        <x-ui.empty-state title="Stok masih aman" description="Belum ada barang yang masuk kategori menipis." icon="check-circle" />
                    @endforelse
                </div>
            </div>
        </section>
    </div>
@endsection

@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
    <x-ui.page-header title="Detail Pelanggan" description="Informasi lengkap pelanggan beserta riwayat transaksi penjualan.">
        <a href="{{ route('master-data.pelanggan.edit', $pelanggan) }}" class="btn btn-secondary">
            <x-ui.icon name="pencil" class="h-4 w-4" />
            <span>Edit</span>
        </a>
        <a href="{{ route('master-data.pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(0,1.6fr)]">

        {{-- Info Pelanggan --}}
        <div class="flex flex-col gap-6">
            <section class="surface divide-y divide-slate-100">
                <div class="px-6 py-4">
                    <h2 class="text-xl font-bold text-slate-900">{{ $pelanggan->nama }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Pelanggan sejak {{ $pelanggan->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Telepon</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $pelanggan->telepon ?: '-' }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $pelanggan->email ?: '-' }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $pelanggan->alamat ?: '-' }}</p>
                </div>
            </section>

            {{-- Statistik --}}
            <section class="surface divide-y divide-slate-100">
                <div class="px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900">Ringkasan Transaksi</h3>
                </div>
                <div class="grid grid-cols-2 divide-x divide-slate-100">
                    <div class="px-6 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Transaksi</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['total_transaksi'] }}</p>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sisa Piutang</p>
                        <p class="mt-1 text-lg font-bold {{ $stats['sisa_piutang'] > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                            Rp {{ number_format($stats['sisa_piutang'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Belanja</p>
                    <p class="mt-1 text-lg font-bold text-brand-700">Rp {{ number_format($stats['total_belanja'], 0, ',', '.') }}</p>
                </div>
            </section>
        </div>

        {{-- Riwayat Penjualan --}}
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900">Riwayat Penjualan</h3>
                <p class="mt-1 text-sm text-slate-500">Semua transaksi penjualan yang terkait dengan pelanggan ini.</p>
            </div>

            @if ($penjualan->isEmpty())
                <x-ui.empty-state title="Belum ada transaksi" description="Pelanggan ini belum memiliki riwayat penjualan." icon="shopping-cart" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Penjualan</th>
                                <th>Pembayaran</th>
                                <th class="!text-right">Total</th>
                                <th class="!text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penjualan as $item)
                                <tr>
                                    <td class="text-slate-600">{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="font-mono text-sm font-semibold text-slate-900">{{ $item->nomor_penjualan }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $item->tipe_pembayaran === 'tunai' ? 'badge-primary' : 'badge-warning' }}">
                                            {{ ucfirst($item->tipe_pembayaran) }}
                                        </span>
                                    </td>
                                    <td class="text-right font-semibold text-slate-900">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = match($item->status_pembayaran) {
                                                'lunas'       => 'badge-success',
                                                'sebagian'    => 'badge-warning',
                                                default       => 'badge-danger',
                                            };
                                            $statusLabel = match($item->status_pembayaran) {
                                                'lunas'       => 'Lunas',
                                                'sebagian'    => 'Sebagian',
                                                default       => 'Belum Lunas',
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
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

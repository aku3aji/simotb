@extends('layouts.app')

@section('title', 'Detail Vendor')

@section('content')
    <x-ui.page-header title="Detail Vendor" description="Informasi lengkap vendor beserta riwayat transaksi pembelian.">
        <a href="{{ route('master-data.vendor.edit', $vendor) }}" class="btn btn-secondary">
            <x-ui.icon name="pencil" class="h-4 w-4" />
            <span>Edit</span>
        </a>
        <a href="{{ route('master-data.vendor.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_minmax(0,1.6fr)]">

        {{-- Info Vendor --}}
        <div class="flex flex-col gap-6">
            <section class="surface divide-y divide-slate-100">
                <div class="px-6 py-4">
                    <h2 class="text-xl font-bold text-slate-900">{{ $vendor->nama }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Vendor sejak {{ $vendor->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Kontak Person</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $vendor->kontak_person ?: '-' }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Telepon</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $vendor->telepon ?: '-' }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Email</p>
                    <p class="mt-1 text-sm font-semibold text-slate-900">{{ $vendor->email ?: '-' }}</p>
                </div>

                <div class="px-6 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Alamat</p>
                    <p class="mt-1 text-sm text-slate-700">{{ $vendor->alamat ?: '-' }}</p>
                </div>
            </section>

            {{-- Statistik --}}
            <section class="surface divide-y divide-slate-100">
                <div class="px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900">Ringkasan Pembelian</h3>
                </div>
                <div class="grid grid-cols-2 divide-x divide-slate-100">
                    <div class="px-6 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Transaksi</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ $stats['total_transaksi'] }}</p>
                    </div>
                    <div class="px-6 py-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Total Pembelian</p>
                        <p class="mt-1 text-lg font-bold text-brand-700">
                            Rp {{ number_format($stats['total_pembelian'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </section>
        </div>

        {{-- Riwayat Pembelian --}}
        <section class="surface overflow-hidden">
            <div class="border-b border-slate-200 px-6 py-4">
                <h3 class="text-lg font-bold text-slate-900">Riwayat Pembelian</h3>
                <p class="mt-1 text-sm text-slate-500">Semua transaksi pembelian yang terkait dengan vendor ini.</p>
            </div>

            @if ($pembelian->isEmpty())
                <x-ui.empty-state title="Belum ada transaksi" description="Vendor ini belum memiliki riwayat pembelian." icon="receipt" />
            @else
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>No. Pembelian</th>
                                <th>Dicatat Oleh</th>
                                <th class="!text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pembelian as $item)
                                <tr>
                                    <td class="text-slate-600">{{ $item->tanggal->format('d/m/Y') }}</td>
                                    <td>
                                        <span class="font-mono text-sm font-semibold text-slate-900">{{ $item->nomor_pembelian }}</span>
                                    </td>
                                    <td class="text-slate-700">{{ $item->user->name ?? '-' }}</td>
                                    <td class="text-right font-semibold text-brand-700">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
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

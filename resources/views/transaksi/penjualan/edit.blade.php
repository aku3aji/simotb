@extends('layouts.app')

@section('title', 'Edit Stok Keluar')

@section('content')
    <x-ui.page-header title="Edit Stok Keluar" description="Perbarui transaksi selama nota belum memiliki pembayaran piutang atau retur.">
        <a href="{{ route('transaksi.stok-keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    @if ($penjualan->returPenjualan->isNotEmpty())
        @php
            $totalRetur = $penjualan->returPenjualan->sum('total_retur');
        @endphp
        <div class="rounded-lg border border-rose-200 bg-rose-50 px-5 py-4">
            <div class="flex items-start gap-3">
                <x-ui.icon name="alert-triangle" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
                <div class="text-sm">
                    <p class="font-semibold text-rose-800">Stok Keluar ini memiliki {{ $penjualan->returPenjualan->count() }} retur (Total retur: Rp {{ number_format($totalRetur, 0, ',', '.') }})</p>
                    <p class="mt-1 text-rose-700">Stok Keluar yang sudah memiliki retur tidak dapat diubah. Lihat detail di halaman <a href="{{ route('transaksi.stok-keluar.show', $penjualan) }}" class="underline font-medium">detail stok keluar</a>.</p>
                </div>
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('transaksi.stok-keluar.update', $penjualan) }}">
        @csrf
        @method('PUT')
        @include('transaksi.penjualan._form', ['submitLabel' => 'Update Stok Keluar'])
    </form>
@endsection

@extends('layouts.app')

@section('title', 'Edit Retur Penjualan')

@section('content')
    <x-ui.page-header title="Edit Retur Penjualan" description="Perbarui detail retur jika ada koreksi jumlah, harga, atau kondisi barang.">
        <a href="{{ route('transaksi.retur-penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.retur-penjualan.update', $returPenjualan) }}">
        @csrf
        @method('PUT')
        @include('transaksi.retur-penjualan._form', ['submitLabel' => 'Update Retur'])
    </form>
@endsection

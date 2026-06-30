@extends('layouts.app')

@section('title', 'Tambah Retur Stok Keluar')

@section('content')
    <x-ui.page-header title="Input Retur Stok Keluar" description="Catat barang yang dikembalikan pelanggan dan kelola dampaknya ke stok.">
        <a href="{{ route('transaksi.retur-stok-keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.retur-stok-keluar.store') }}">
        @csrf
        @include('transaksi.retur-penjualan._form', ['submitLabel' => 'Simpan Retur', 'nomorRetur' => $nomorRetur])
    </form>
@endsection

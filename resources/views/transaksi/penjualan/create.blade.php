@extends('layouts.app')

@section('title', 'Tambah Stok Keluar')

@section('content')
    <x-ui.page-header title="Input Stok Keluar" description="Kelola nota dengan tampilan sederhana dan cepat untuk admin operasional.">
        <a href="{{ route('transaksi.stok-keluar.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.stok-keluar.store') }}">
        @csrf
        @include('transaksi.penjualan._form', ['submitLabel' => 'Simpan Stok Keluar'])
    </form>
@endsection

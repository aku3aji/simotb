@extends('layouts.app')

@section('title', 'Tambah Penjualan')

@section('content')
    <x-ui.page-header title="Input Penjualan" description="Kelola nota kasir dengan tampilan sederhana dan cepat untuk admin operasional.">
        <a href="{{ route('transaksi.penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.penjualan.store') }}">
        @csrf
        @include('transaksi.penjualan._form', ['submitLabel' => 'Simpan Penjualan'])
    </form>
@endsection

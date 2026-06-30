@extends('layouts.app')

@section('title', 'Tambah Stok Masuk')

@section('content')
    <x-ui.page-header title="Input Stok Masuk" description="Catat barang masuk dari vendor dan otomatis tambahkan stok ke sistem.">
        <a href="{{ route('transaksi.stok-masuk.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.stok-masuk.store') }}">
        @csrf
        @include('transaksi.pembelian._form', ['submitLabel' => 'Simpan Stok Masuk'])
    </form>
@endsection

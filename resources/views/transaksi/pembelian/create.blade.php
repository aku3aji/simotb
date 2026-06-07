@extends('layouts.app')

@section('title', 'Tambah Pembelian')

@section('content')
    <x-ui.page-header title="Input Pembelian" description="Catat barang masuk dari vendor dan otomatis tambahkan stok ke sistem.">
        <a href="{{ route('transaksi.pembelian.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.pembelian.store') }}">
        @csrf
        @include('transaksi.pembelian._form', ['submitLabel' => 'Simpan Pembelian'])
    </form>
@endsection

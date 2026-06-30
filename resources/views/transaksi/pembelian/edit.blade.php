@extends('layouts.app')

@section('title', 'Edit Stok Masuk')

@section('content')
    <x-ui.page-header title="Edit Stok Masuk" description="Perbarui transaksi stok masuk jika ada koreksi jumlah atau harga beli.">
        <a href="{{ route('transaksi.stok-masuk.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.stok-masuk.update', $pembelian) }}">
        @csrf
        @method('PUT')
        @include('transaksi.pembelian._form', ['submitLabel' => 'Update Stok Masuk'])
    </form>
@endsection

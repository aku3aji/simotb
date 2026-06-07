@extends('layouts.app')

@section('title', 'Edit Pembelian')

@section('content')
    <x-ui.page-header title="Edit Pembelian" description="Perbarui transaksi pembelian jika ada koreksi jumlah atau harga beli.">
        <a href="{{ route('transaksi.pembelian.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.pembelian.update', $pembelian) }}">
        @csrf
        @method('PUT')
        @include('transaksi.pembelian._form', ['submitLabel' => 'Update Pembelian'])
    </form>
@endsection

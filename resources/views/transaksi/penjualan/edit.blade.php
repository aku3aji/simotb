@extends('layouts.app')

@section('title', 'Edit Penjualan')

@section('content')
    <x-ui.page-header title="Edit Penjualan" description="Perbarui transaksi selama nota belum memiliki pembayaran piutang atau retur.">
        <a href="{{ route('transaksi.penjualan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.penjualan.update', $penjualan) }}">
        @csrf
        @method('PUT')
        @include('transaksi.penjualan._form', ['submitLabel' => 'Update Penjualan'])
    </form>
@endsection

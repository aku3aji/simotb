@extends('layouts.app')

@section('title', 'Tambah Pembayaran Utang')

@section('content')
    <x-ui.page-header title="Input Pembayaran Utang" description="Catat cicilan atau pelunasan utang ke vendor dari transaksi stok masuk kredit.">
        <a href="{{ route('transaksi.pembayaran-utang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.pembayaran-utang.store') }}">
        @csrf
        @include('transaksi.pembayaran-utang._form', ['submitLabel' => 'Simpan Pembayaran'])
    </form>
@endsection

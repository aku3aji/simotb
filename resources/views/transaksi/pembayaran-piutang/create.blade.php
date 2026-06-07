@extends('layouts.app')

@section('title', 'Tambah Pembayaran Piutang')

@section('content')
    <x-ui.page-header title="Input Pembayaran Piutang" description="Catat cicilan atau pelunasan dari transaksi kredit pelanggan.">
        <a href="{{ route('transaksi.pembayaran-piutang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.pembayaran-piutang.store') }}">
        @csrf
        @include('transaksi.pembayaran-piutang._form', ['submitLabel' => 'Simpan Pembayaran'])
    </form>
@endsection

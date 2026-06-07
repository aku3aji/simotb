@extends('layouts.app')

@section('title', 'Edit Pembayaran Piutang')

@section('content')
    <x-ui.page-header title="Edit Pembayaran Piutang" description="Perbarui nominal, metode, atau catatan pembayaran kredit.">
        <a href="{{ route('transaksi.pembayaran-piutang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.pembayaran-piutang.update', $pembayaranPiutang) }}">
        @csrf
        @method('PUT')
        @include('transaksi.pembayaran-piutang._form', ['submitLabel' => 'Update Pembayaran'])
    </form>
@endsection

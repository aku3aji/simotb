@extends('layouts.app')

@section('title', 'Edit Pembayaran Utang')

@section('content')
    <x-ui.page-header title="Edit Pembayaran Utang" description="Perbarui nominal, metode, atau catatan pembayaran utang.">
        <a href="{{ route('transaksi.pembayaran-utang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('transaksi.pembayaran-utang.update', $pembayaranUtang) }}">
        @csrf
        @method('PUT')
        @include('transaksi.pembayaran-utang._form', ['submitLabel' => 'Update Pembayaran'])
    </form>
@endsection

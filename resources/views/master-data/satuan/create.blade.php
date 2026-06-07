@extends('layouts.app')

@section('title', 'Tambah Satuan')

@section('content')
    <x-ui.page-header title="Tambah Satuan" description="Satuan akan dipakai di data barang, pembelian, dan penjualan.">
        <a href="{{ route('master-data.satuan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.satuan.store') }}">
        @csrf
        @include('master-data.satuan._form', ['submitLabel' => 'Simpan Satuan'])
    </form>
@endsection

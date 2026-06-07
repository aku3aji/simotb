@extends('layouts.app')

@section('title', 'Tambah Barang')

@section('content')
    <x-ui.page-header title="Tambah Barang" description="Masukkan barang baru beserta harga, stok awal, dan batas minimum.">
        <a href="{{ route('master-data.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.barang.store') }}">
        @csrf
        @include('master-data.barang._form', ['submitLabel' => 'Simpan Barang'])
    </form>
@endsection

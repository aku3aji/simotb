@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <x-ui.page-header title="Edit Barang" description="Perbarui harga, stok, atau atribut barang sesuai kondisi terbaru.">
        <a href="{{ route('master-data.barang.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.barang.update', $barang) }}">
        @csrf
        @method('PUT')
        @include('master-data.barang._form', ['submitLabel' => 'Update Barang'])
    </form>
@endsection

@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <x-ui.page-header title="Edit Kategori" description="Perbarui nama atau deskripsi kategori barang.">
        <a href="{{ route('master-data.kategori.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.kategori.update', $kategori) }}">
        @csrf
        @method('PUT')
        @include('master-data.kategori._form', ['submitLabel' => 'Update Kategori'])
    </form>
@endsection

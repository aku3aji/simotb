@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <x-ui.page-header title="Tambah Kategori" description="Kelompokkan barang agar pencarian dan pelaporan lebih rapi.">
        <a href="{{ route('master-data.kategori.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.kategori.store') }}">
        @csrf
        @include('master-data.kategori._form', ['submitLabel' => 'Simpan Kategori'])
    </form>
@endsection

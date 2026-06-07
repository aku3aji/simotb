@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('content')
    <x-ui.page-header title="Tambah Pelanggan" description="Simpan pelanggan tetap untuk memudahkan transaksi tunai dan kredit.">
        <a href="{{ route('master-data.pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.pelanggan.store') }}">
        @csrf
        @include('master-data.pelanggan._form', ['submitLabel' => 'Simpan Pelanggan'])
    </form>
@endsection

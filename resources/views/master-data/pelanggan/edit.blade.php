@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')
    <x-ui.page-header title="Edit Pelanggan" description="Perbarui identitas pelanggan jika ada perubahan kontak atau alamat.">
        <a href="{{ route('master-data.pelanggan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.pelanggan.update', $pelanggan) }}">
        @csrf
        @method('PUT')
        @include('master-data.pelanggan._form', ['submitLabel' => 'Update Pelanggan'])
    </form>
@endsection

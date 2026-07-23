@extends('layouts.app')

@section('title', 'Edit Pegawai')

@section('content')
    <x-ui.page-header title="Edit Pegawai" description="Perbarui informasi pegawai jika ada perubahan jabatan atau kontak.">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('pegawai.pegawai.update', $pegawai) }}">
        @csrf
        @method('PUT')
        @include('pegawai.pegawai._form', ['submitLabel' => 'Update Pegawai'])
    </form>
@endsection

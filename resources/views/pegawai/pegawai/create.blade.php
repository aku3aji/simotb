@extends('layouts.app')

@section('title', 'Tambah Pegawai')

@section('content')
    <x-ui.page-header title="Tambah Pegawai" description="Simpan data pegawai yang terlibat di operasional toko.">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('pegawai.pegawai.store') }}">
        @csrf
        @include('pegawai.pegawai._form', ['submitLabel' => 'Simpan Pegawai'])
    </form>
@endsection

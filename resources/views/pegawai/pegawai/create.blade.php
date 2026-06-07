@extends('layouts.app')

@section('title', 'Tambah Pegawai')

@section('content')
    <x-ui.page-header title="Tambah Pegawai" description="Simpan data pegawai yang terlibat di operasional toko.">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn {{ request()->routeIs('pegawai.pegawai.*') ? 'btn-primary' : 'btn-secondary' }}">Data Pegawai</a>
        <a href="{{ route('pegawai.absensi.index') }}" class="btn {{ request()->routeIs('pegawai.absensi.*') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
    </div>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('pegawai.pegawai.store') }}">
        @csrf
        @include('pegawai.pegawai._form', ['submitLabel' => 'Simpan Pegawai'])
    </form>
@endsection

@extends('layouts.app')

@section('title', 'Edit Absensi')

@section('content')
    <x-ui.page-header title="Edit Absensi" description="Perbarui data kehadiran apabila ada koreksi jam atau status.">
        <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        @if (auth()->user()->isOwner())
            <a href="{{ route('pegawai.pegawai.index') }}" class="btn {{ request()->routeIs('pegawai.pegawai.*') ? 'btn-primary' : 'btn-secondary' }}">Data Pegawai</a>
        @endif
        <a href="{{ route('pegawai.absensi.index') }}" class="btn {{ request()->routeIs('pegawai.absensi.*') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
    </div>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('pegawai.absensi.update', $absensi) }}">
        @csrf
        @method('PUT')
        @include('pegawai.absensi._form', ['submitLabel' => 'Update Absensi'])
    </form>
@endsection

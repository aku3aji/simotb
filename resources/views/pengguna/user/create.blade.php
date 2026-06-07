@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <x-ui.page-header title="Tambah User" description="Buat akun baru untuk owner atau admin operasional.">
        <a href="{{ route('pengguna.user.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('pengguna.user.store') }}">
        @csrf
        @include('pengguna.user._form', ['submitLabel' => 'Simpan User'])
    </form>
@endsection

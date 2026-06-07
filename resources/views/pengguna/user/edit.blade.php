@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
    <x-ui.page-header title="Edit User" description="Perbarui role, email, atau status aktif akun pengguna.">
        <a href="{{ route('pengguna.user.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('pengguna.user.update', $user) }}">
        @csrf
        @method('PUT')
        @include('pengguna.user._form', ['submitLabel' => 'Update User'])
    </form>
@endsection

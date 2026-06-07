@extends('layouts.app')

@section('title', 'Tambah Merek')

@section('content')
    <x-ui.page-header title="Tambah Merek" description="Simpan daftar merek yang dijual toko.">
        <a href="{{ route('master-data.merek.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.merek.store') }}">
        @csrf
        @include('master-data.merek._form', ['submitLabel' => 'Simpan Merek'])
    </form>
@endsection

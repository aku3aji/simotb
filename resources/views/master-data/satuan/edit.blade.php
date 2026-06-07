@extends('layouts.app')

@section('title', 'Edit Satuan')

@section('content')
    <x-ui.page-header title="Edit Satuan" description="Perbarui nama atau singkatan satuan barang.">
        <a href="{{ route('master-data.satuan.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.satuan.update', $satuan) }}">
        @csrf
        @method('PUT')
        @include('master-data.satuan._form', ['submitLabel' => 'Update Satuan'])
    </form>
@endsection

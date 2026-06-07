@extends('layouts.app')

@section('title', 'Edit Merek')

@section('content')
    <x-ui.page-header title="Edit Merek" description="Perbarui identitas merek barang.">
        <a href="{{ route('master-data.merek.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.merek.update', $merek) }}">
        @csrf
        @method('PUT')
        @include('master-data.merek._form', ['submitLabel' => 'Update Merek'])
    </form>
@endsection

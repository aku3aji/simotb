@extends('layouts.app')

@section('title', 'Tambah Vendor')

@section('content')
    <x-ui.page-header title="Tambah Vendor" description="Simpan data pemasok agar proses pembelian lebih cepat dan rapi.">
        <a href="{{ route('master-data.vendor.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.vendor.store') }}">
        @csrf
        @include('master-data.vendor._form', ['submitLabel' => 'Simpan Vendor'])
    </form>
@endsection

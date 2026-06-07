@extends('layouts.app')

@section('title', 'Edit Vendor')

@section('content')
    <x-ui.page-header title="Edit Vendor" description="Perbarui kontak vendor untuk menjaga alur pembelian tetap lancar.">
        <a href="{{ route('master-data.vendor.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('master-data.vendor.update', $vendor) }}">
        @csrf
        @method('PUT')
        @include('master-data.vendor._form', ['submitLabel' => 'Update Vendor'])
    </form>
@endsection

@extends('layouts.app')

@section('title', 'Vendor')

@section('content')
    <x-ui.page-header title="Vendor" description="Kelola daftar pemasok utama untuk pembelian barang toko.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('master-data.vendor.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Tambah Vendor</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('master-data.vendor.bulk-destroy') }}"
          data-confirm="Hapus semua vendor yang dipilih? Tindakan ini tidak dapat dibatalkan.">
        @csrf
        @method('DELETE')
    </form>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari vendor, PIC, telepon, atau email">
                </div>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($vendor->isEmpty())
            <x-ui.empty-state title="Vendor belum tersedia" description="Tambahkan vendor untuk memulai pencatatan pembelian." icon="truck" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <th>Vendor</th>
                            <th>Kontak</th>
                            <th>Alamat</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendor as $item)
                            <tr>
                                <td class="!px-3"><input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->nama }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $item->kontak_person ?: 'Tanpa PIC' }}</div>
                                </td>
                                <td>
                                    <div>{{ $item->telepon ?: '-' }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $item->email ?: '-' }}</div>
                                </td>
                                <td>{{ $item->alamat ?: '-' }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('master-data.vendor.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('master-data.vendor.destroy', $item) }}" data-confirm="Hapus vendor '{{ $item->nama }}'?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger px-3 py-2">
                                                <x-ui.icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $vendor->links() }}
        @endif
    </section>
@endsection

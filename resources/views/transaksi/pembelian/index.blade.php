@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')
    <x-ui.page-header title="Transaksi Pembelian" description="Catat barang masuk dari vendor dan pantau total pembelian per periode.">
        <a href="{{ route('transaksi.pembelian.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Input Pembelian</span>
        </a>
    </x-ui.page-header>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_220px_180px_180px_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nomor pembelian atau vendor">
                </div>
                <select name="vendor_id" class="select-field">
                    <option value="">Semua vendor</option>
                    @foreach ($vendorList as $item)
                        <option value="{{ $item->id }}" @selected($vendorId == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($pembelian->isEmpty())
            <x-ui.empty-state title="Belum ada pembelian" description="Transaksi pembelian akan tampil di sini setelah Anda mencatat barang masuk." icon="receipt" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Vendor</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Pencatat</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pembelian as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_pembelian }}</td>
                                <td>{{ $item->vendor->nama ?? '-' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="font-semibold text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>{{ $item->user->name ?? '-' }}</td>
                                <td>
                                    <div class="flex justify-center">
                                        <a href="{{ route('transaksi.pembelian.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $pembelian->links() }}
        @endif
    </section>
@endsection

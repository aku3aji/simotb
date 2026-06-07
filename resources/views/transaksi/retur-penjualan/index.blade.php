@extends('layouts.app')

@section('title', 'Retur Penjualan')

@section('content')
    <x-ui.page-header title="Retur Penjualan" description="Catat barang yang dikembalikan pelanggan dan sesuaikan stok sesuai kondisinya.">
        <a href="{{ route('transaksi.retur-penjualan.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Buat Retur</span>
        </a>
    </x-ui.page-header>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_180px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nomor retur, nomor penjualan, atau pelanggan">
                </div>
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($returPenjualan->isEmpty())
            <x-ui.empty-state title="Belum ada retur penjualan" description="Retur akan muncul di sini setelah pelanggan mengembalikan barang." icon="rotate-ccw" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <x-ui.sortable-th column="nomor_retur" label="Nomor Retur" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Penjualan</th>
                            <th>Pelanggan</th>
                            <x-ui.sortable-th column="tanggal" label="Tanggal" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Total Retur</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returPenjualan as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_retur }}</td>
                                <td>{{ $item->penjualan->nomor_penjualan ?? '-' }}</td>
                                <td>{{ $item->penjualan->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td class="font-semibold text-slate-900">Rp {{ number_format($item->total_retur, 0, ',', '.') }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('transaksi.retur-penjualan.show', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="eye" class="h-4 w-4" />
                                        </a>
                                        <a href="{{ route('transaksi.retur-penjualan.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        <form method="POST" action="{{ route('transaksi.retur-penjualan.destroy', $item) }}" data-confirm="Hapus retur penjualan '{{ $item->nomor_retur }}'?">
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

            {{ $returPenjualan->links() }}
        @endif
    </section>
@endsection

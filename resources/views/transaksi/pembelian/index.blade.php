@extends('layouts.app')

@section('title', 'Pembelian')

@section('content')
    <x-ui.page-header title="Transaksi Pembelian" description="Catat barang masuk dari vendor dan pantau total pembelian per periode.">
        <a href="{{ route('transaksi.pembelian.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Buat Pembelian</span>
        </a>
    </x-ui.page-header>

    <div class="flex flex-wrap items-center gap-6 rounded-lg border border-slate-200 bg-white px-5 py-3 text-sm">
        <div>
            <span class="text-slate-500">Hari ini:</span>
            <span class="ml-1.5 font-semibold text-slate-900">Rp {{ number_format($statHariIni->total, 0, ',', '.') }}</span>
            <span class="ml-1 text-slate-400">({{ $statHariIni->count }} transaksi)</span>
        </div>
        <div class="h-4 w-px bg-slate-200"></div>
        <div>
            <span class="text-slate-500">Bulan ini:</span>
            <span class="ml-1.5 font-semibold text-slate-900">Rp {{ number_format($statBulanIni->total, 0, ',', '.') }}</span>
            <span class="ml-1 text-slate-400">({{ $statBulanIni->count }} transaksi)</span>
        </div>
    </div>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_220px_180px_180px_auto_auto]">
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
                <select name="per_page" class="select-field" onchange="this.form.submit()">
                    <option value="10" @selected($perPage == 10)>10 / hal</option>
                    <option value="25" @selected($perPage == 25)>25 / hal</option>
                    <option value="50" @selected($perPage == 50)>50 / hal</option>
                </select>
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
                            <x-ui.sortable-th column="nomor_pembelian" label="Nomor" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Vendor</th>
                            <x-ui.sortable-th column="tanggal" label="Tanggal" :sort-by="$sortBy" :sort-dir="$sortDir" />
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

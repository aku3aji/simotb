@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <x-ui.page-header title="Transaksi Penjualan" description="Catat transaksi tunai maupun kredit dengan ringkasan pembayaran yang jelas.">
        <a href="{{ route('transaksi.penjualan.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Buat Penjualan</span>
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
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_180px_180px_180px_180px_auto_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nomor penjualan atau pelanggan">
                </div>
                <select name="tipe_pembayaran" class="select-field">
                    <option value="">Semua tipe</option>
                    <option value="tunai" @selected($tipePembayaran === 'tunai')>Tunai</option>
                    <option value="kredit" @selected($tipePembayaran === 'kredit')>Kredit</option>
                </select>
                <select name="status_pembayaran" class="select-field">
                    <option value="">Semua status</option>
                    <option value="lunas" @selected($statusPembayaran === 'lunas')>Lunas</option>
                    <option value="sebagian" @selected($statusPembayaran === 'sebagian')>Sebagian</option>
                    <option value="belum_lunas" @selected($statusPembayaran === 'belum_lunas')>Belum Lunas</option>
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

        @if ($penjualan->isEmpty())
            <x-ui.empty-state title="Belum ada penjualan" description="Transaksi penjualan akan muncul di sini setelah mulai mencatat nota." icon="shopping-cart" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <x-ui.sortable-th column="nomor_penjualan" label="Nomor" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Pelanggan</th>
                            <x-ui.sortable-th column="tanggal" label="Tanggal" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <x-ui.sortable-th column="tipe_pembayaran" label="Tipe" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th>Total</th>
                            <th>Piutang</th>
                            <x-ui.sortable-th column="status_pembayaran" label="Status" :sort-by="$sortBy" :sort-dir="$sortDir" />
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $item)
                            <tr>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</div>
                                    @if ($item->retur_penjualan_count > 0)
                                        <span class="badge badge-warning mt-1">Ada Retur</span>
                                    @endif
                                </td>
                                <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $item->tipe_pembayaran === 'tunai' ? 'badge-success' : 'badge-warning' }}">
                                        {{ ucfirst($item->tipe_pembayaran) }}
                                    </span>
                                </td>
                                <td class="font-semibold text-slate-900">
                                    @php $totalEfektif = $item->total - ($item->retur_penjualan_sum_total_retur ?? 0); @endphp
                                    Rp {{ number_format($totalEfektif, 0, ',', '.') }}
                                    @if (($item->retur_penjualan_sum_total_retur ?? 0) > 0)
                                        <div class="mt-0.5 text-xs font-normal text-slate-400 line-through">Rp {{ number_format($item->total, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $item->status_pembayaran === 'lunas' ? 'badge-success' : ($item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('transaksi.penjualan.show', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="eye" class="h-4 w-4" />
                                        </a>
                                        <a href="{{ route('transaksi.penjualan.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $penjualan->links() }}
        @endif
    </section>
@endsection

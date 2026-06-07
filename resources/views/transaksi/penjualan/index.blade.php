@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <x-ui.page-header title="Kasir Penjualan" description="Catat transaksi tunai maupun kredit dengan ringkasan pembayaran yang jelas.">
        <a href="{{ route('transaksi.penjualan.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Buat Penjualan</span>
        </a>
    </x-ui.page-header>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_180px_180px_180px_180px_auto]">
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
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($penjualan->isEmpty())
            <x-ui.empty-state title="Belum ada penjualan" description="Transaksi penjualan akan muncul di sini setelah kasir mulai mencatat nota." icon="shopping-cart" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nomor</th>
                            <th>Pelanggan</th>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Total</th>
                            <th>Piutang</th>
                            <th>Status</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualan as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->nomor_penjualan }}</td>
                                <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge {{ $item->tipe_pembayaran === 'tunai' ? 'badge-success' : 'badge-warning' }}">
                                        {{ ucfirst($item->tipe_pembayaran) }}
                                    </span>
                                </td>
                                <td class="font-semibold text-slate-900">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge {{ $item->status_pembayaran === 'lunas' ? 'badge-success' : ($item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex justify-center">
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

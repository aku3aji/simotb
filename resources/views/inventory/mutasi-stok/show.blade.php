@extends('layouts.app')

@section('title', 'Riwayat Mutasi — ' . $barang->nama)

@section('content')
    <x-ui.page-header
        title="Riwayat Mutasi Stok"
        description="{{ $barang->nama }} ({{ $barang->kode_barang }})">
        <a href="{{ route('inventory.mutasi-stok.index') }}" class="btn btn-secondary">
            <x-ui.icon name="chevron-left" class="h-4 w-4" />
            <span>Kembali</span>
        </a>
    </x-ui.page-header>

    {{-- Info Barang --}}
    <section class="surface mb-6 p-5">
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Kategori</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $barang->kategori->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Satuan</p>
                <p class="mt-1 font-semibold text-slate-900">{{ $barang->satuan->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Stok Minimum</p>
                <p class="mt-1 font-semibold text-slate-900">{{ number_format($barang->stok_minimum, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Stok Saat Ini</p>
                <p class="mt-1 text-2xl font-extrabold {{ $barang->stok <= $barang->stok_minimum ? 'text-rose-700' : 'text-emerald-700' }}">
                    {{ number_format($barang->stok, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_220px_180px_180px_auto_auto]">
                <select name="tipe" class="select-field">
                    <option value="">Semua tipe</option>
                    <option value="masuk" @selected($tipe === 'masuk')>Masuk</option>
                    <option value="keluar" @selected($tipe === 'keluar')>Keluar</option>
                    <option value="penyesuaian" @selected($tipe === 'penyesuaian')>Penyesuaian</option>
                </select>
                <select name="sumber" class="select-field">
                    <option value="">Semua sumber</option>
                    <option value="pembelian" @selected($sumber === 'pembelian')>Pembelian</option>
                    <option value="penjualan" @selected($sumber === 'penjualan')>Penjualan</option>
                    <option value="retur_penjualan" @selected($sumber === 'retur_penjualan')>Retur Penjualan</option>
                    <option value="stock_opname" @selected($sumber === 'stock_opname')>Stock Opname</option>
                    <option value="manual" @selected($sumber === 'manual')>Manual</option>
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

        @if ($mutasi->isEmpty())
            <x-ui.empty-state title="Belum ada riwayat mutasi" description="Belum ada perubahan stok yang tercatat untuk barang ini." icon="arrow-right-left" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Sumber</th>
                            <th class="!text-right">Jumlah</th>
                            <th class="!text-right">Stok Sebelum</th>
                            <th class="!text-right">Stok Sesudah</th>
                            <th>Keterangan</th>
                            <th>Dicatat Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mutasi as $item)
                            @php
                                $tipeClass = match($item->tipe) {
                                    'masuk' => 'badge-success',
                                    'keluar' => 'badge-danger',
                                    default => 'badge-warning',
                                };
                                $sumberLabel = match($item->sumber) {
                                    'pembelian' => 'Pembelian',
                                    'penjualan' => 'Penjualan',
                                    'retur_penjualan' => 'Retur Penjualan',
                                    'stock_opname' => 'Stock Opname',
                                    default => ucfirst($item->sumber),
                                };
                                $jumlahSign = $item->tipe === 'masuk' ? '+' : ($item->tipe === 'keluar' ? '-' : '±');
                                $jumlahClass = $item->tipe === 'masuk' ? 'text-emerald-700 font-bold' : ($item->tipe === 'keluar' ? 'text-rose-700 font-bold' : 'text-amber-700 font-bold');
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap text-slate-500">{{ optional($item->created_at)->translatedFormat('d M Y, H:i') }}</td>
                                <td><span class="badge {{ $tipeClass }}">{{ ucfirst($item->tipe) }}</span></td>
                                <td class="text-slate-600">{{ $sumberLabel }}</td>
                                <td class="text-right {{ $jumlahClass }}">{{ $jumlahSign }}{{ $item->jumlah }}</td>
                                <td class="text-right text-slate-600">{{ $item->stok_sebelum }}</td>
                                <td class="text-right font-semibold text-slate-900">{{ $item->stok_sesudah }}</td>
                                <td class="max-w-[220px] truncate text-sm text-slate-500">{{ $item->keterangan ?: '-' }}</td>
                                <td class="text-slate-500">{{ $item->user->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $mutasi->links() }}
        @endif
    </section>
@endsection

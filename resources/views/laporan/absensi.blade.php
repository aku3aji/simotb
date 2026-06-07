@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
    <x-ui.page-header title="Laporan Absensi Pegawai" description="Rekap kehadiran pegawai dalam periode tertentu.">
        <a href="{{ route('laporan.absensi', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn btn-secondary">
            <x-ui.icon name="table" class="h-4 w-4" />
            <span>Export Excel</span>
        </a>
        <a href="{{ route('laporan.absensi', array_merge(request()->query(), ['export' => 'pdf'])) }}" class="btn btn-primary">
            <x-ui.icon name="file-text" class="h-4 w-4" />
            <span>Export PDF</span>
        </a>
    </x-ui.page-header>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        <a href="{{ route('laporan.stok') }}" class="btn {{ request()->routeIs('laporan.stok') ? 'btn-primary' : 'btn-secondary' }}">Stok</a>
        <a href="{{ route('laporan.pembelian') }}" class="btn {{ request()->routeIs('laporan.pembelian') ? 'btn-primary' : 'btn-secondary' }}">Pembelian</a>
        <a href="{{ route('laporan.penjualan') }}" class="btn {{ request()->routeIs('laporan.penjualan') ? 'btn-primary' : 'btn-secondary' }}">Penjualan</a>
        <a href="{{ route('laporan.piutang') }}" class="btn {{ request()->routeIs('laporan.piutang') ? 'btn-primary' : 'btn-secondary' }}">Piutang</a>
        <a href="{{ route('laporan.absensi') }}" class="btn {{ request()->routeIs('laporan.absensi') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
    </div>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 sm:grid-cols-[180px_180px_minmax(0,1fr)_auto]">
                <input type="date" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="input-field">
                <input type="date" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="input-field">
                <select name="pegawai_id" class="select-field">
                    <option value="">Semua pegawai</option>
                    @foreach ($pegawaiList as $pegawai)
                        <option value="{{ $pegawai->id }}" @selected($pegawaiId === $pegawai->id)>{{ $pegawai->nama }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($absensi->isEmpty())
            <x-ui.empty-state title="Belum ada data absensi" description="Tidak ada catatan absensi dalam periode yang dipilih." icon="users" />
        @else
            {{-- Ringkasan --}}
            <div class="border-b border-slate-200 bg-slate-50/60 px-5 py-4">
                <div class="flex flex-wrap gap-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Total Catatan</p>
                        <p class="mt-1 text-xl font-bold text-slate-900">{{ $absensi->count() }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Hadir</p>
                        <p class="mt-1 text-xl font-bold text-emerald-700">{{ $summary['hadir'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Izin</p>
                        <p class="mt-1 text-xl font-bold text-amber-700">{{ $summary['izin'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Sakit</p>
                        <p class="mt-1 text-xl font-bold text-blue-700">{{ $summary['sakit'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Alpha</p>
                        <p class="mt-1 text-xl font-bold text-rose-700">{{ $summary['alpha'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Ringkasan Gaji --}}
            @if ($gajiSummary->isNotEmpty())
                <div class="border-b border-slate-200 px-5 py-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-500">Rekap Gaji Berdasarkan Kehadiran</p>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Pegawai</th>
                                    <th>Jabatan</th>
                                    <th>Hari Hadir</th>
                                    <th>Gaji / Hari</th>
                                    <th>Total Gaji</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gajiSummary as $row)
                                    <tr>
                                        <td class="font-semibold text-slate-900">{{ $row['nama'] }}</td>
                                        <td class="text-slate-500">{{ $row['jabatan'] }}</td>
                                        <td>{{ $row['jumlah_hadir'] }} hari</td>
                                        <td>
                                            @if ($row['gaji_harian'] > 0)
                                                Rp {{ number_format($row['gaji_harian'], 0, ',', '.') }}
                                            @else
                                                <span class="text-slate-400">–</span>
                                            @endif
                                        </td>
                                        <td class="font-semibold text-slate-900">
                                            @if ($row['total_gaji'] > 0)
                                                Rp {{ number_format($row['total_gaji'], 0, ',', '.') }}
                                            @else
                                                <span class="text-slate-400">–</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            @if ($totalGaji > 0)
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="font-semibold text-slate-700">Total Seluruh Gaji</td>
                                        <td class="font-bold text-slate-900">Rp {{ number_format($totalGaji, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Pegawai</th>
                            <th>Jabatan</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($absensi as $item)
                            <tr>
                                <td class="font-semibold text-slate-900">{{ $item->pegawai->nama ?? '-' }}</td>
                                <td class="text-slate-500">{{ $item->pegawai->jabatan ?? '-' }}</td>
                                <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                                <td>{{ $item->jam_masuk ?? '-' }}</td>
                                <td>{{ $item->jam_keluar ?? '-' }}</td>
                                <td>
                                    @php
                                        $badgeClass = match($item->status) {
                                            'hadir'  => 'badge-success',
                                            'izin'   => 'badge-warning',
                                            'sakit'  => 'badge-primary',
                                            'alpha'  => 'badge-danger',
                                            default  => 'badge-muted',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td class="text-slate-500">{{ $item->keterangan ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 10px; color: #1e293b; margin: 18mm 15mm; }

        .kop { background: #1e40af; color: #fff; padding: 11px 14px; margin-bottom: 14px; }
        .kop-nama { font-size: 15px; font-weight: 700; letter-spacing: 0.01em; }
        .kop-sub { font-size: 8.5px; margin-top: 3px; opacity: 0.82; }

        .doc-info { margin-bottom: 13px; padding-bottom: 9px; border-bottom: 2px solid #e2e8f0; }
        .doc-info h2 { font-size: 13px; font-weight: 700; color: #1e40af; }
        .doc-info p { font-size: 9px; color: #64748b; margin-top: 3px; }

        .stats { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .stats td { padding-right: 6px; vertical-align: top; }
        .stats td:last-child { padding-right: 0; }
        .stat-box { border: 1px solid #e2e8f0; border-top: 3px solid #1e40af; background: #f8fafc; padding: 8px 10px; }
        .stat-box.green { border-top-color: #15803d; }
        .stat-box.amber { border-top-color: #b45309; }
        .stat-box.blue { border-top-color: #1d4ed8; }
        .stat-box.red { border-top-color: #b91c1c; }
        .stat-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
        .stat-value { font-size: 12.5px; font-weight: 700; color: #0f172a; margin-top: 3px; }
        .stat-box.green .stat-value { color: #15803d; }
        .stat-box.amber .stat-value { color: #b45309; }
        .stat-box.blue .stat-value { color: #1d4ed8; }
        .stat-box.red .stat-value { color: #b91c1c; }

        .section-title { font-size: 10px; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.05em; margin: 16px 0 8px; border-left: 3px solid #1e40af; padding-left: 6px; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        table.data tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; color: #334155; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }
        table.data tfoot td { padding: 7px 8px; font-weight: 700; font-size: 10px; background: #eff6ff; border-top: 2px solid #1e40af; color: #1e40af; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 8px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-info { background: #dbeafe; color: #1d4ed8; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }

        .doc-footer { margin-top: 14px; padding-top: 7px; border-top: 1px solid #e2e8f0; width: 100%; border-collapse: collapse; }
        .doc-footer td { font-size: 8px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="kop">
        <div class="kop-nama">Toko Bangunan Sumber Alam Jaya</div>
        <div class="kop-sub">Sistem Informasi Manajemen Operasional Toko Bangunan &mdash; SIMOTB</div>
    </div>

    <div class="doc-info">
        <h2>Laporan Absensi Pegawai</h2>
        <p>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}
            @if ($tanggalMulai || $tanggalSelesai)
                &nbsp;&mdash;&nbsp;Periode:
                {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }}
                s/d
                {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}
            @endif
            @if ($pegawaiId > 0)
                &nbsp;&mdash;&nbsp;Pegawai: {{ $pegawaiList->firstWhere('id', $pegawaiId)?->nama ?? '-' }}
            @endif
        </p>
    </div>

    <table class="stats">
        <tr>
            <td width="20%">
                <div class="stat-box">
                    <div class="stat-label">Total Catatan</div>
                    <div class="stat-value">{{ $absensi->count() }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="stat-box green">
                    <div class="stat-label">Hadir</div>
                    <div class="stat-value">{{ $summary['hadir'] }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="stat-box amber">
                    <div class="stat-label">Izin</div>
                    <div class="stat-value">{{ $summary['izin'] }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="stat-box blue">
                    <div class="stat-label">Sakit</div>
                    <div class="stat-value">{{ $summary['sakit'] }}</div>
                </div>
            </td>
            <td width="20%">
                <div class="stat-box red">
                    <div class="stat-label">Alpha</div>
                    <div class="stat-value">{{ $summary['alpha'] }}</div>
                </div>
            </td>
        </tr>
    </table>

    @if ($gajiSummary->isNotEmpty())
        <div class="section-title">Rekap Gaji Berdasarkan Kehadiran</div>
        <table class="data" style="margin-bottom:16px">
            <thead>
                <tr>
                    <th>Pegawai</th>
                    <th>Jabatan</th>
                    <th class="text-center">Hari Hadir</th>
                    <th class="text-right">Gaji / Hari</th>
                    <th class="text-right">Total Gaji</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gajiSummary as $row)
                    <tr>
                        <td style="font-weight:700">{{ $row['nama'] }}</td>
                        <td>{{ $row['jabatan'] }}</td>
                        <td class="text-center">{{ $row['jumlah_hadir'] }} hari</td>
                        <td class="text-right">
                            {{ $row['gaji_harian'] > 0 ? 'Rp ' . number_format($row['gaji_harian'], 0, ',', '.') : '–' }}
                        </td>
                        <td class="text-right" style="font-weight:700">
                            {{ $row['total_gaji'] > 0 ? 'Rp ' . number_format($row['total_gaji'], 0, ',', '.') : '–' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            @if ($totalGaji > 0)
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">Total Seluruh Gaji</td>
                        <td class="text-right">Rp {{ number_format($totalGaji, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @endif

    <div class="section-title">Detail Absensi</div>
    <table class="data">
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
                    <td style="font-weight:700">{{ $item->pegawai->nama ?? '-' }}</td>
                    <td>{{ $item->pegawai->jabatan ?? '-' }}</td>
                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>{{ $item->jam_masuk ?? '-' }}</td>
                    <td>{{ $item->jam_keluar ?? '-' }}</td>
                    <td>
                        @php
                            $badgeClass = match($item->status) {
                                'hadir'  => 'badge-success',
                                'izin'   => 'badge-warning',
                                'sakit'  => 'badge-info',
                                'alpha'  => 'badge-danger',
                                default  => '',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td>{{ $item->keterangan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="doc-footer">
        <tr>
            <td>SIMOTB &copy; {{ now()->year }} Sumber Alam Jaya</td>
            <td style="text-align:right">Dokumen ini dicetak secara otomatis oleh sistem</td>
        </tr>
    </table>
</body>
</html>

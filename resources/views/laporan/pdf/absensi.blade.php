<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 12px; }
        .header h1 { font-size: 16px; font-weight: 700; color: #1d4ed8; }
        .header p { font-size: 10px; color: #64748b; margin-top: 2px; }
        .summary { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; gap: 24px; }
        .summary-item p { font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; }
        .summary-item span { font-size: 13px; font-weight: 700; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-primary { background: #dbeafe; color: #1d4ed8; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
        .section-title { font-size: 10px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: 0.05em; margin: 16px 0 8px; }
        tfoot td { padding: 6px 10px; font-size: 10px; font-weight: 700; border-top: 2px solid #e2e8f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toko Bangunan Sumber Alam Jaya</h1>
        <p>Laporan Absensi Pegawai &mdash; Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>
        @if ($tanggalMulai || $tanggalSelesai)
            <p>Periode: {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }} s/d {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}</p>
        @endif
        @if ($pegawaiId > 0)
            <p>Filter pegawai: {{ $pegawaiList->firstWhere('id', $pegawaiId)?->nama ?? '-' }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <p>Total Catatan</p>
            <span>{{ $absensi->count() }}</span>
        </div>
        <div class="summary-item">
            <p style="color:#15803d">Hadir</p>
            <span style="color:#15803d">{{ $summary['hadir'] }}</span>
        </div>
        <div class="summary-item">
            <p style="color:#b45309">Izin</p>
            <span style="color:#b45309">{{ $summary['izin'] }}</span>
        </div>
        <div class="summary-item">
            <p style="color:#1d4ed8">Sakit</p>
            <span style="color:#1d4ed8">{{ $summary['sakit'] }}</span>
        </div>
        <div class="summary-item">
            <p style="color:#b91c1c">Alpha</p>
            <span style="color:#b91c1c">{{ $summary['alpha'] }}</span>
        </div>
    </div>

    @if ($gajiSummary->isNotEmpty())
        <p class="section-title">Rekap Gaji Berdasarkan Kehadiran</p>
        <table style="margin-bottom:16px">
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
                        <td style="font-weight:600">{{ $row['nama'] }}</td>
                        <td>{{ $row['jabatan'] }}</td>
                        <td class="text-center">{{ $row['jumlah_hadir'] }} hari</td>
                        <td class="text-right">
                            @if ($row['gaji_harian'] > 0)
                                Rp {{ number_format($row['gaji_harian'], 0, ',', '.') }}
                            @else
                                –
                            @endif
                        </td>
                        <td class="text-right" style="font-weight:600">
                            @if ($row['total_gaji'] > 0)
                                Rp {{ number_format($row['total_gaji'], 0, ',', '.') }}
                            @else
                                –
                            @endif
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

    <p class="section-title">Detail Absensi</p>
    <table>
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
                    <td style="font-weight:600">{{ $item->pegawai->nama ?? '-' }}</td>
                    <td>{{ $item->pegawai->jabatan ?? '-' }}</td>
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

    <div class="footer">Dokumen ini dicetak secara otomatis oleh sistem SIMOTB &mdash; Sumber Alam Jaya</div>
</body>
</html>

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
        .stat-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
        .stat-value { font-size: 12.5px; font-weight: 700; color: #0f172a; margin-top: 3px; }
        .stat-box.green .stat-value { color: #15803d; }
        .stat-box.amber .stat-value { color: #b45309; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        table.data tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; color: #334155; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }
        table.data tfoot td { padding: 7px 8px; font-weight: 700; font-size: 10px; background: #eff6ff; border-top: 2px solid #1e40af; color: #1e40af; }

        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 8px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
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
        <h2>Laporan Penjualan</h2>
        <p>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}
            @if ($tanggalMulai || $tanggalSelesai)
                &nbsp;&mdash;&nbsp;Periode:
                {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }}
                s/d
                {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}
            @endif
            @if ($tipePembayaran)
                &nbsp;&mdash;&nbsp;Tipe: {{ ucfirst($tipePembayaran) }}
            @endif
        </p>
    </div>

    <table class="stats">
        <tr>
            <td width="25%">
                <div class="stat-box">
                    <div class="stat-label">Jumlah Transaksi</div>
                    <div class="stat-value">{{ $penjualan->count() }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="stat-box">
                    <div class="stat-label">Total Penjualan</div>
                    <div class="stat-value">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="stat-box green">
                    <div class="stat-label">Tunai</div>
                    <div class="stat-value">Rp {{ number_format($totalTunai, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="25%">
                <div class="stat-box amber">
                    <div class="stat-label">Kredit</div>
                    <div class="stat-value">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Pelanggan</th>
                <th>Tanggal</th>
                <th>Tipe</th>
                <th class="text-right">Total</th>
                <th class="text-right">Dibayar</th>
                <th class="text-right">Sisa Piutang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penjualan as $item)
                <tr>
                    <td>{{ $item->nomor_penjualan }}</td>
                    <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>
                        <span class="badge {{ $item->tipe_pembayaran === 'tunai' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($item->tipe_pembayaran) }}
                        </span>
                    </td>
                    <td class="text-right" style="font-weight:700">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->dibayar, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $item->status_pembayaran === 'lunas' ? 'badge-success' : ($item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger') }}">
                            {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total {{ $penjualan->count() }} transaksi</td>
                <td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                <td colspan="3"></td>
            </tr>
        </tfoot>
    </table>

    <table class="doc-footer">
        <tr>
            <td>SIMOTB &copy; {{ now()->year }} Sumber Alam Jaya</td>
            <td style="text-align:right">Dokumen ini dicetak secara otomatis oleh sistem</td>
        </tr>
    </table>
</body>
</html>

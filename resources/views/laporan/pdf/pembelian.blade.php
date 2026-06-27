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
        .stat-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
        .stat-value { font-size: 12.5px; font-weight: 700; color: #0f172a; margin-top: 3px; }

        table.data { width: 100%; border-collapse: collapse; }
        table.data thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        table.data tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; color: #334155; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }
        table.data tfoot td { padding: 7px 8px; font-weight: 700; font-size: 10px; background: #eff6ff; border-top: 2px solid #1e40af; color: #1e40af; }

        .text-right { text-align: right; }

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
        <h2>Laporan Pembelian</h2>
        <p>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}
            @if ($tanggalMulai || $tanggalSelesai)
                &nbsp;&mdash;&nbsp;Periode:
                {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }}
                s/d
                {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}
            @endif
        </p>
    </div>

    <table class="stats">
        <tr>
            <td width="50%">
                <div class="stat-box">
                    <div class="stat-label">Jumlah Transaksi</div>
                    <div class="stat-value">{{ $pembelian->count() }}</div>
                </div>
            </td>
            <td width="50%">
                <div class="stat-box">
                    <div class="stat-label">Total Pembelian</div>
                    <div class="stat-value">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Vendor</th>
                <th>Tanggal</th>
                <th>Dicatat Oleh</th>
                <th class="text-right">Total</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian as $item)
                <tr>
                    <td>{{ $item->nomor_pembelian }}</td>
                    <td>{{ $item->vendor->nama ?? '-' }}</td>
                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td class="text-right" style="font-weight:700">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ $item->catatan ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">Total {{ $pembelian->count() }} transaksi</td>
                <td class="text-right">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</td>
                <td></td>
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

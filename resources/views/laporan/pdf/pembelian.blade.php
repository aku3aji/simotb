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
        .summary { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; gap: 32px; }
        .summary-item p { font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; }
        .summary-item span { font-size: 14px; font-weight: 700; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 11px; background: #f1f5f9; border-top: 2px solid #e2e8f0; }
        .text-right { text-align: right; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toko Bangunan Sumber Alam Jaya</h1>
        <p>Laporan Pembelian &mdash; Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>
        @if ($tanggalMulai || $tanggalSelesai)
            <p>Periode: {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }} s/d {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <p>Jumlah Transaksi</p>
            <span>{{ $pembelian->count() }}</span>
        </div>
        <div class="summary-item">
            <p>Total Pembelian</p>
            <span>Rp {{ number_format($totalPembelian, 0, ',', '.') }}</span>
        </div>
    </div>

    <table>
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
                    <td class="text-right" style="font-weight:600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
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

    <div class="footer">Dokumen ini dicetak secara otomatis oleh sistem SIMOTB &mdash; Sumber Alam Jaya</div>
</body>
</html>

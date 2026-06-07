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
        .summary { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; gap: 28px; }
        .summary-item p { font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; }
        .summary-item span { font-size: 13px; font-weight: 700; color: #0f172a; }
        .summary-item.tunai span { color: #15803d; }
        .summary-item.kredit span { color: #b45309; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 11px; background: #f1f5f9; border-top: 2px solid #e2e8f0; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toko Bangunan Sumber Alam Jaya</h1>
        <p>Laporan Penjualan &mdash; Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>
        @if ($tanggalMulai || $tanggalSelesai)
            <p>Periode: {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }} s/d {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}</p>
        @endif
        @if ($tipePembayaran)
            <p>Filter tipe: {{ ucfirst($tipePembayaran) }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <p>Jumlah Transaksi</p>
            <span>{{ $penjualan->count() }}</span>
        </div>
        <div class="summary-item">
            <p>Total Penjualan</p>
            <span>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item tunai">
            <p>Tunai</p>
            <span>Rp {{ number_format($totalTunai, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item kredit">
            <p>Kredit</p>
            <span>Rp {{ number_format($totalKredit, 0, ',', '.') }}</span>
        </div>
    </div>

    <table>
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
                    <td class="text-right" style="font-weight:600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
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

    <div class="footer">Dokumen ini dicetak secara otomatis oleh sistem SIMOTB &mdash; Sumber Alam Jaya</div>
</body>
</html>

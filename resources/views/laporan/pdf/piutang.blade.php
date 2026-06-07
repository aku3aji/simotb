<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #b91c1c; padding-bottom: 10px; margin-bottom: 12px; }
        .header h1 { font-size: 16px; font-weight: 700; color: #1d4ed8; }
        .header p { font-size: 10px; color: #64748b; margin-top: 2px; }
        .summary { background: #fff1f2; border: 1px solid #fecdd3; border-radius: 6px; padding: 10px 14px; margin-bottom: 14px; display: flex; gap: 32px; }
        .summary-item p { font-size: 9px; font-weight: 600; text-transform: uppercase; color: #64748b; }
        .summary-item span { font-size: 14px; font-weight: 700; color: #0f172a; }
        .summary-item.total span { color: #b91c1c; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f1f5f9; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 11px; background: #fff1f2; border-top: 2px solid #fecdd3; }
        .text-right { text-align: right; }
        .overdue { color: #b91c1c; font-weight: 700; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
        .badge-warning { background: #fef9c3; color: #854d0e; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toko Bangunan Sumber Alam Jaya</h1>
        <p>Laporan Piutang (Belum Lunas) &mdash; Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>
        @if ($tanggalMulai || $tanggalSelesai)
            <p>Filter jatuh tempo: {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }} s/d {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}</p>
        @endif
    </div>

    <div class="summary">
        <div class="summary-item">
            <p>Jumlah Debitur</p>
            <span>{{ $piutang->count() }}</span>
        </div>
        <div class="summary-item total">
            <p>Total Piutang Tersisa</p>
            <span>Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No. Penjualan</th>
                <th>Pelanggan</th>
                <th>Tgl. Transaksi</th>
                <th>Jatuh Tempo</th>
                <th class="text-right">Total</th>
                <th class="text-right">Sudah Dibayar</th>
                <th class="text-right">Sisa Piutang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($piutang as $item)
                @php $lewat = $item->jatuh_tempo && $item->jatuh_tempo->isPast(); @endphp
                <tr>
                    <td>{{ $item->nomor_penjualan }}</td>
                    <td>{{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}</td>
                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                    <td class="{{ $lewat ? 'overdue' : '' }}">
                        {{ optional($item->jatuh_tempo)->translatedFormat('d M Y') ?? '-' }}
                        @if ($lewat) &#9888; @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->dibayar, 0, ',', '.') }}</td>
                    <td class="text-right" style="font-weight:700;color:#b91c1c">Rp {{ number_format($item->sisa_piutang, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge {{ $item->status_pembayaran === 'sebagian' ? 'badge-warning' : 'badge-danger' }}">
                            {{ str_replace('_', ' ', ucfirst($item->status_pembayaran)) }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">Total {{ $piutang->count() }} piutang belum lunas</td>
                <td class="text-right" style="color:#b91c1c">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">Dokumen ini dicetak secara otomatis oleh sistem SIMOTB &mdash; Sumber Alam Jaya</div>
</body>
</html>

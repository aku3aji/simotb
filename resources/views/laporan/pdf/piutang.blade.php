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
        .doc-info h2 { font-size: 13px; font-weight: 700; color: #b91c1c; }
        .doc-info p { font-size: 9px; color: #64748b; margin-top: 3px; }

        .stats { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .stats td { padding-right: 6px; vertical-align: top; }
        .stats td:last-child { padding-right: 0; }
        .stat-box { border: 1px solid #e2e8f0; border-top: 3px solid #1e40af; background: #f8fafc; padding: 8px 10px; }
        .stat-box.red { border-top-color: #b91c1c; background: #fff5f5; }
        .stat-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
        .stat-value { font-size: 12.5px; font-weight: 700; color: #0f172a; margin-top: 3px; }
        .stat-box.red .stat-value { color: #b91c1c; }

        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data th, table.data td { word-wrap: break-word; overflow-wrap: break-word; }
        table.data thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        table.data tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; color: #334155; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }
        table.data tfoot td { padding: 7px 8px; font-weight: 700; font-size: 10px; background: #fff5f5; border-top: 2px solid #b91c1c; color: #b91c1c; }

        .text-right { text-align: right; }
        .overdue { color: #b91c1c; font-weight: 700; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 8px; font-weight: 700; }
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
        <h2>Laporan Piutang (Belum Lunas)</h2>
        <p>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}
            @if ($tanggalMulai || $tanggalSelesai)
                &nbsp;&mdash;&nbsp;Filter jatuh tempo:
                {{ $tanggalMulai ? \Carbon\Carbon::parse($tanggalMulai)->translatedFormat('d M Y') : '...' }}
                s/d
                {{ $tanggalSelesai ? \Carbon\Carbon::parse($tanggalSelesai)->translatedFormat('d M Y') : '...' }}
            @endif
        </p>
    </div>

    @php
        $overdueCount = $piutang->filter(fn($p) => $p->jatuh_tempo && $p->jatuh_tempo->isPast())->count();
    @endphp

    <table class="stats">
        <tr>
            <td width="33%">
                <div class="stat-box">
                    <div class="stat-label">Jumlah Debitur</div>
                    <div class="stat-value">{{ $piutang->count() }}</div>
                </div>
            </td>
            <td width="33%">
                <div class="stat-box red">
                    <div class="stat-label">Total Piutang Tersisa</div>
                    <div class="stat-value">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
                </div>
            </td>
            <td width="34%">
                <div class="stat-box red">
                    <div class="stat-label">Lewat Jatuh Tempo</div>
                    <div class="stat-value">{{ $overdueCount }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <colgroup>
            <col style="width:13%"><col style="width:17%"><col style="width:11%"><col style="width:11%">
            <col style="width:12%"><col style="width:12%"><col style="width:12%"><col style="width:12%">
        </colgroup>
        <thead>
            <tr>
                <th>No. Stok Keluar</th>
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
                <td class="text-right">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
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

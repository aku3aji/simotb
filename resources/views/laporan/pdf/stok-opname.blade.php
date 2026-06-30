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

        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data th, table.data td { word-wrap: break-word; overflow-wrap: break-word; }
        table.data thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        table.data tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; color: #334155; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }

        .text-right { text-align: right; }
        .pos { color: #15803d; font-weight: 700; }
        .neg { color: #b91c1c; font-weight: 700; }

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
        <h2>Laporan Stok Opname</h2>
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
                    <div class="stat-label">Jumlah Sesi</div>
                    <div class="stat-value">{{ $totalSesi }}</div>
                </div>
            </td>
            <td width="50%">
                <div class="stat-box">
                    <div class="stat-label">Total Item Diperiksa</div>
                    <div class="stat-value">{{ number_format($totalItem) }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <colgroup>
            <col style="width:16%"><col style="width:13%"><col style="width:12%">
            <col style="width:12%"><col style="width:27%"><col style="width:20%">
        </colgroup>
        <thead>
            <tr>
                <th>Nomor Opname</th>
                <th>Tanggal</th>
                <th class="text-right">Jumlah Item</th>
                <th class="text-right">Total Selisih</th>
                <th>Catatan</th>
                <th>Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($opname as $item)
                @php $selisih = (int) ($item->detail_sum_selisih ?? 0); @endphp
                <tr>
                    <td>{{ $item->nomor_opname }}</td>
                    <td>{{ optional($item->tanggal)->translatedFormat('d M Y') }}</td>
                    <td class="text-right">{{ number_format($item->detail_count) }}</td>
                    <td class="text-right {{ $selisih > 0 ? 'pos' : ($selisih < 0 ? 'neg' : '') }}">{{ $selisih > 0 ? '+' : '' }}{{ number_format($selisih) }}</td>
                    <td>{{ $item->catatan ?: '-' }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
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

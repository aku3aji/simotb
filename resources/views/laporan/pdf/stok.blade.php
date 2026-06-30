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
        .stat-box.red { border-top-color: #b91c1c; }
        .stat-label { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #64748b; }
        .stat-value { font-size: 12.5px; font-weight: 700; color: #0f172a; margin-top: 3px; }
        .stat-box.red .stat-value { color: #b91c1c; }

        table.data { width: 100%; border-collapse: collapse; table-layout: fixed; }
        table.data th, table.data td { word-wrap: break-word; overflow-wrap: break-word; }
        table.data thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
        table.data tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 9.5px; color: #334155; vertical-align: middle; }
        table.data tbody tr:nth-child(even) td { background: #f8fafc; }
        table.data tfoot td { padding: 7px 8px; font-weight: 700; font-size: 10px; background: #eff6ff; border-top: 2px solid #1e40af; color: #1e40af; }

        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 5px; font-size: 8px; font-weight: 700; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .stok-low { color: #b91c1c; font-weight: 700; }

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
        <h2>Laporan Stok Barang</h2>
        <p>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}
            @if ($kategoriId > 0)
                &nbsp;&mdash;&nbsp;Kategori: {{ $kategoriList->firstWhere('id', $kategoriId)?->nama ?? '-' }}
            @endif
        </p>
    </div>

    @php
        $stokMenipis = $barang->filter(fn($b) => $b->stok <= $b->stok_minimum)->count();
    @endphp

    <table class="stats">
        <tr>
            <td width="33%">
                <div class="stat-box">
                    <div class="stat-label">Total Jenis Barang</div>
                    <div class="stat-value">{{ $barang->count() }}</div>
                </div>
            </td>
            <td width="33%">
                <div class="stat-box">
                    <div class="stat-label">Total Stok</div>
                    <div class="stat-value">{{ number_format($barang->sum('stok')) }}</div>
                </div>
            </td>
            <td width="34%">
                <div class="stat-box red">
                    <div class="stat-label">Stok Menipis</div>
                    <div class="stat-value">{{ $stokMenipis }}</div>
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <colgroup>
            <col style="width:11%"><col style="width:22%"><col style="width:13%"><col style="width:12%"><col style="width:9%">
            <col style="width:8%"><col style="width:9%"><col style="width:9%"><col style="width:7%">
        </colgroup>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Merek</th>
                <th>Satuan</th>
                <th class="text-right">Stok</th>
                <th class="text-right">Stok Min.</th>
                <th class="text-right">Harga Jual</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($barang as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>{{ $item->kategori->nama ?? '-' }}</td>
                    <td>{{ $item->merek->nama ?? '-' }}</td>
                    <td>{{ $item->satuan->nama ?? '-' }}</td>
                    <td class="text-right {{ $item->stok <= $item->stok_minimum ? 'stok-low' : '' }}">{{ number_format($item->stok) }}</td>
                    <td class="text-right">{{ number_format($item->stok_minimum) }}</td>
                    <td class="text-right">Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                    <td>
                        @if ($item->stok <= $item->stok_minimum)
                            <span class="badge badge-danger">Menipis</span>
                        @else
                            <span class="badge badge-success">Aman</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">Total {{ $barang->count() }} barang</td>
                <td class="text-right">{{ number_format($barang->sum('stok')) }}</td>
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

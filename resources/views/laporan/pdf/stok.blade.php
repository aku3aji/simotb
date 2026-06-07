<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #1d4ed8; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; font-weight: 700; color: #1d4ed8; }
        .header p { font-size: 10px; color: #64748b; margin-top: 2px; }
        .meta { font-size: 10px; color: #64748b; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        thead th { background: #f1f5f9; padding: 7px 10px; text-align: left; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; font-size: 10px; color: #334155; }
        tbody tr:nth-child(even) td { background: #f8fafc; }
        tfoot td { padding: 8px 10px; font-weight: 700; font-size: 11px; background: #f1f5f9; border-top: 2px solid #e2e8f0; }
        .text-right { text-align: right; }
        .badge { display: inline-block; padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: 600; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-success { background: #dcfce7; color: #15803d; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Toko Bangunan Sumber Alam Jaya</h1>
        <p>Laporan Stok Barang &mdash; Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }}</p>
        @if ($kategoriId > 0)
            <p class="meta">Filter kategori: {{ $kategoriList->firstWhere('id', $kategoriId)?->nama ?? '-' }}</p>
        @endif
    </div>

    <table>
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
                    <td class="text-right" style="{{ $item->stok <= $item->stok_minimum ? 'color:#b91c1c;font-weight:700' : '' }}">{{ number_format($item->stok) }}</td>
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

    <div class="footer">Dokumen ini dicetak secara otomatis oleh sistem SIMOTB &mdash; Sumber Alam Jaya</div>
</body>
</html>

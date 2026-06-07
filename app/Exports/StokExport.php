<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StokExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(private readonly int $kategoriId = 0) {}

    public function collection()
    {
        return Barang::query()
            ->with(['kategori', 'satuan', 'merek'])
            ->when($this->kategoriId > 0, fn ($q) => $q->where('kategori_id', $this->kategoriId))
            ->orderBy('nama')
            ->get()
            ->map(fn ($item, $i) => [
                'No'         => $i + 1,
                'Kode'       => $item->kode_barang,
                'Nama'       => $item->nama,
                'Kategori'   => $item->kategori->nama ?? '-',
                'Merek'      => $item->merek->nama ?? '-',
                'Satuan'     => $item->satuan->nama ?? '-',
                'Stok'       => $item->stok,
                'Stok Min.'  => $item->stok_minimum,
                'Harga Jual' => (float) $item->harga_jual,
                'Status'     => $item->stok <= $item->stok_minimum ? 'Menipis' : 'Aman',
            ]);
    }

    public function headings(): array
    {
        return ['No', 'Kode', 'Nama Barang', 'Kategori', 'Merek', 'Satuan', 'Stok', 'Stok Min.', 'Harga Jual', 'Status'];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
        ];
    }
}

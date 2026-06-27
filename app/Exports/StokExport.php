<?php

namespace App\Exports;

use App\Models\Barang;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StokExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(private readonly int $kategoriId = 0) {}

    public function query(): Builder
    {
        return Barang::query()
            ->with(['kategori', 'satuan', 'merek'])
            ->when($this->kategoriId > 0, fn ($q) => $q->where('kategori_id', $this->kategoriId))
            ->orderBy('nama');
    }

    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $item->kode_barang,
            $item->nama,
            $item->kategori->nama ?? '-',
            $item->merek->nama ?? '-',
            $item->satuan->nama ?? '-',
            $item->stok,
            $item->stok_minimum,
            (float) $item->harga_jual,
            $item->stok <= $item->stok_minimum ? 'Menipis' : 'Aman',
        ];
    }

    public function headings(): array
    {
        return ['No', 'Kode', 'Nama Barang', 'Kategori', 'Merek', 'Satuan', 'Stok', 'Stok Min.', 'Harga Jual', 'Status'];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
        ];
    }
}

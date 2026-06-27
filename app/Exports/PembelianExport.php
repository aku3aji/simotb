<?php

namespace App\Exports;

use App\Models\Pembelian;
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

class PembelianExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
    ) {}

    public function query(): Builder
    {
        return Pembelian::query()
            ->with(['vendor', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->where('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->where('tanggal', '<=', $this->tanggalSelesai))
            ->latest('tanggal')
            ->latest('id');
    }

    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $item->nomor_pembelian,
            $item->vendor->nama ?? '-',
            optional($item->tanggal)->format('d/m/Y'),
            $item->user->name ?? '-',
            (float) $item->total,
            $item->catatan ?? '',
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nomor Pembelian', 'Vendor', 'Tanggal', 'Dicatat Oleh', 'Total (Rp)', 'Catatan'];
    }

    public function title(): string
    {
        return 'Laporan Pembelian';
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

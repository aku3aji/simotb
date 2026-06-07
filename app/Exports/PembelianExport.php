<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PembelianExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
    ) {}

    public function collection()
    {
        return Pembelian::query()
            ->with(['vendor', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->whereDate('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->whereDate('tanggal', '<=', $this->tanggalSelesai))
            ->latest('tanggal')
            ->latest('id')
            ->get()
            ->map(fn ($item, $i) => [
                'No'          => $i + 1,
                'Nomor'       => $item->nomor_pembelian,
                'Vendor'      => $item->vendor->nama ?? '-',
                'Tanggal'     => optional($item->tanggal)->format('d/m/Y'),
                'Dicatat'     => $item->user->name ?? '-',
                'Total'       => (float) $item->total,
                'Catatan'     => $item->catatan ?? '',
            ]);
    }

    public function headings(): array
    {
        return ['No', 'Nomor Pembelian', 'Vendor', 'Tanggal', 'Dicatat Oleh', 'Total (Rp)', 'Catatan'];
    }

    public function title(): string
    {
        return 'Laporan Pembelian';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
        ];
    }
}

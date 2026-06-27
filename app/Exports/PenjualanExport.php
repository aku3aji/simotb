<?php

namespace App\Exports;

use App\Models\Penjualan;
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

class PenjualanExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
        private readonly string $tipePembayaran = '',
    ) {}

    public function query(): Builder
    {
        return Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->where('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->where('tanggal', '<=', $this->tanggalSelesai))
            ->when($this->tipePembayaran !== '', fn ($q) => $q->where('tipe_pembayaran', $this->tipePembayaran))
            ->latest('tanggal')
            ->latest('id');
    }

    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $item->nomor_penjualan,
            $item->pelanggan->nama ?? 'Pelanggan Umum',
            optional($item->tanggal)->format('d/m/Y'),
            ucfirst($item->tipe_pembayaran),
            (float) $item->total,
            (float) $item->dibayar,
            (float) $item->sisa_piutang,
            str_replace('_', ' ', ucfirst($item->status_pembayaran)),
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nomor Penjualan', 'Pelanggan', 'Tanggal', 'Tipe', 'Total (Rp)', 'Dibayar (Rp)', 'Sisa Piutang (Rp)', 'Status'];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
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

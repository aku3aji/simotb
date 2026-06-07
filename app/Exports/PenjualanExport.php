<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenjualanExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
        private readonly string $tipePembayaran = '',
    ) {}

    public function collection()
    {
        return Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->whereDate('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->whereDate('tanggal', '<=', $this->tanggalSelesai))
            ->when($this->tipePembayaran !== '', fn ($q) => $q->where('tipe_pembayaran', $this->tipePembayaran))
            ->latest('tanggal')
            ->latest('id')
            ->get()
            ->map(fn ($item, $i) => [
                'No'              => $i + 1,
                'Nomor'           => $item->nomor_penjualan,
                'Pelanggan'       => $item->pelanggan->nama ?? 'Pelanggan Umum',
                'Tanggal'         => optional($item->tanggal)->format('d/m/Y'),
                'Tipe'            => ucfirst($item->tipe_pembayaran),
                'Total'           => (float) $item->total,
                'Dibayar'         => (float) $item->dibayar,
                'Sisa Piutang'    => (float) $item->sisa_piutang,
                'Status'          => str_replace('_', ' ', ucfirst($item->status_pembayaran)),
            ]);
    }

    public function headings(): array
    {
        return ['No', 'Nomor Penjualan', 'Pelanggan', 'Tanggal', 'Tipe', 'Total (Rp)', 'Dibayar (Rp)', 'Sisa Piutang (Rp)', 'Status'];
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
        ];
    }
}

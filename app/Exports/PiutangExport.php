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

class PiutangExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
    ) {}

    public function collection()
    {
        return Penjualan::query()
            ->with(['pelanggan', 'user'])
            ->kredit()
            ->belumLunas()
            ->when($this->tanggalMulai !== '', fn ($q) => $q->whereDate('jatuh_tempo', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->whereDate('jatuh_tempo', '<=', $this->tanggalSelesai))
            ->latest('jatuh_tempo')
            ->latest('id')
            ->get()
            ->map(fn ($item, $i) => [
                'No'              => $i + 1,
                'Nomor'           => $item->nomor_penjualan,
                'Pelanggan'       => $item->pelanggan->nama ?? 'Pelanggan Umum',
                'Tgl. Transaksi'  => optional($item->tanggal)->format('d/m/Y'),
                'Jatuh Tempo'     => optional($item->jatuh_tempo)->format('d/m/Y') ?? '-',
                'Total'           => (float) $item->total,
                'Sudah Dibayar'   => (float) $item->dibayar,
                'Sisa Piutang'    => (float) $item->sisa_piutang,
                'Status'          => str_replace('_', ' ', ucfirst($item->status_pembayaran)),
            ]);
    }

    public function headings(): array
    {
        return ['No', 'Nomor Penjualan', 'Pelanggan', 'Tgl. Transaksi', 'Jatuh Tempo', 'Total (Rp)', 'Sudah Dibayar (Rp)', 'Sisa Piutang (Rp)', 'Status'];
    }

    public function title(): string
    {
        return 'Laporan Piutang';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFECDD3']]],
        ];
    }
}

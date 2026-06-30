<?php

namespace App\Exports;

use App\Models\StokMutasi;
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

class MutasiStokExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
        private readonly string $tipe = '',
        private readonly string $sumber = '',
    ) {}

    public function query(): Builder
    {
        $allowedTipe = [StokMutasi::TIPE_MASUK, StokMutasi::TIPE_KELUAR, StokMutasi::TIPE_PENYESUAIAN];
        $allowedSumber = [
            StokMutasi::SUMBER_PEMBELIAN,
            StokMutasi::SUMBER_PENJUALAN,
            StokMutasi::SUMBER_RETUR_PENJUALAN,
            StokMutasi::SUMBER_STOCK_OPNAME,
            StokMutasi::SUMBER_MANUAL,
        ];

        return StokMutasi::query()
            ->with(['barang', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->whereDate('created_at', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->whereDate('created_at', '<=', $this->tanggalSelesai))
            ->when(in_array($this->tipe, $allowedTipe), fn ($q) => $q->where('tipe', $this->tipe))
            ->when(in_array($this->sumber, $allowedSumber), fn ($q) => $q->where('sumber', $this->sumber))
            ->latest('id');
    }

    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            optional($item->created_at)->format('d/m/Y H:i'),
            $item->barang->kode_barang ?? '-',
            $item->barang->nama ?? '-',
            ucfirst($item->tipe),
            str_replace('_', ' ', ucfirst($item->sumber)),
            (int) $item->jumlah,
            (int) $item->stok_sebelum,
            (int) $item->stok_sesudah,
            $item->keterangan ?? '',
            $item->user->name ?? '-',
        ];
    }

    public function headings(): array
    {
        return ['No', 'Waktu', 'Kode', 'Barang', 'Tipe', 'Sumber', 'Jumlah', 'Stok Sebelum', 'Stok Sesudah', 'Keterangan', 'Dicatat Oleh'];
    }

    public function title(): string
    {
        return 'Laporan Mutasi Stok';
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

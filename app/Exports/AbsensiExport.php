<?php

namespace App\Exports;

use App\Models\Absensi;
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

class AbsensiExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
        private readonly int $pegawaiId = 0,
    ) {}

    public function query(): Builder
    {
        return Absensi::query()
            ->with(['pegawai', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->where('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->where('tanggal', '<=', $this->tanggalSelesai))
            ->when($this->pegawaiId > 0, fn ($q) => $q->where('pegawai_id', $this->pegawaiId))
            ->latest('tanggal')
            ->latest('id');
    }

    public function map($item): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $item->pegawai->nama ?? '-',
            $item->pegawai->jabatan ?? '-',
            optional($item->tanggal)->format('d/m/Y'),
            $item->jam_masuk ?? '-',
            $item->jam_keluar ?? '-',
            ucfirst($item->status),
            $item->keterangan ?? '',
            $item->pegawai->gaji_harian ?? 0,
            $item->status === 'hadir' ? ($item->pegawai->gaji_harian ?? 0) : 0,
        ];
    }

    public function headings(): array
    {
        return ['No', 'Pegawai', 'Jabatan', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Keterangan', 'Gaji/Hari (Rp)', 'Total Gaji (Rp)'];
    }

    public function title(): string
    {
        return 'Laporan Absensi';
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

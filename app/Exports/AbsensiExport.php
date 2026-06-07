<?php

namespace App\Exports;

use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AbsensiExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly string $tanggalMulai = '',
        private readonly string $tanggalSelesai = '',
        private readonly int $pegawaiId = 0,
    ) {}

    public function collection()
    {
        return Absensi::query()
            ->with(['pegawai', 'user'])
            ->when($this->tanggalMulai !== '', fn ($q) => $q->whereDate('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($q) => $q->whereDate('tanggal', '<=', $this->tanggalSelesai))
            ->when($this->pegawaiId > 0, fn ($q) => $q->where('pegawai_id', $this->pegawaiId))
            ->latest('tanggal')
            ->latest('id')
            ->get()
            ->map(fn ($item, $i) => [
                'No'          => $i + 1,
                'Pegawai'     => $item->pegawai->nama ?? '-',
                'Jabatan'     => $item->pegawai->jabatan ?? '-',
                'Tanggal'     => optional($item->tanggal)->format('d/m/Y'),
                'Jam Masuk'   => $item->jam_masuk ?? '-',
                'Jam Keluar'  => $item->jam_keluar ?? '-',
                'Status'      => ucfirst($item->status),
                'Keterangan'  => $item->keterangan ?? '',
                'Gaji/Hari'   => $item->pegawai->gaji_harian ?? 0,
                'Total Gaji'  => $item->status === 'hadir' ? ($item->pegawai->gaji_harian ?? 0) : 0,
            ]);
    }

    public function headings(): array
    {
        return ['No', 'Pegawai', 'Jabatan', 'Tanggal', 'Jam Masuk', 'Jam Keluar', 'Status', 'Keterangan', 'Gaji/Hari (Rp)', 'Total Gaji (Rp)'];
    }

    public function title(): string
    {
        return 'Laporan Absensi';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE2E8F0']]],
        ];
    }
}

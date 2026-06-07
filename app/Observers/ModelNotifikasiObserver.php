<?php

namespace App\Observers;

use App\Services\NotifikasiService;
use Illuminate\Database\Eloquent\Model;

class ModelNotifikasiObserver
{
    public function __construct(
        private string $label,
        private string $ikon,
        private string $warna,
        private \Closure $namaFn,
        private ?\Closure $tautanFn = null,
    ) {}

    public function created(Model $model): void
    {
        $oleh = auth()->user()?->name ?? 'Sistem';
        NotifikasiService::catat(
            "{$this->label} Ditambahkan",
            ($this->namaFn)($model) . " ditambahkan oleh {$oleh}.",
            $this->ikon,
            'success',
            $this->tautanFn ? ($this->tautanFn)($model) : null,
        );
    }

    public function updated(Model $model): void
    {
        $oleh = auth()->user()?->name ?? 'Sistem';
        NotifikasiService::catat(
            "{$this->label} Diperbarui",
            ($this->namaFn)($model) . " diperbarui oleh {$oleh}.",
            $this->ikon,
            $this->warna,
            $this->tautanFn ? ($this->tautanFn)($model) : null,
        );
    }

    public function deleted(Model $model): void
    {
        $oleh = auth()->user()?->name ?? 'Sistem';
        NotifikasiService::catat(
            "{$this->label} Dihapus",
            ($this->namaFn)($model) . " dihapus oleh {$oleh}.",
            $this->ikon,
            'danger',
            null,
        );
    }
}

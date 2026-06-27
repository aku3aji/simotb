@extends('layouts.app')

@section('title', 'Tambah Stock Opname')

@section('content')
    @php
        $barangOptions = $barangList->map(fn ($item) => [
            'id'       => $item->id,
            'kode'     => $item->kode_barang,
            'nama'     => $item->nama,
            'stok'     => (int) $item->stok,
            'kategori' => $item->kategori->nama ?? '-',
            'satuan'   => $item->satuan->singkatan ?? $item->satuan->nama ?? '',
        ])->values();

        // Restore old values per barang_id after validation fail
        $oldDetail = collect(old('detail', []))->keyBy('barang_id');
    @endphp

    <x-ui.page-header title="Buat Stock Opname" description="Isi stok fisik untuk barang yang diopname. Barang yang dikosongkan dianggap tidak ada penyesuaian.">
        <a href="{{ route('inventory.stok-opname.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('inventory.stok-opname.store') }}" class="space-y-6">
        @csrf

        <section class="surface p-6">
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <label class="label-text" for="nomor_opname">Nomor Opname</label>
                    <input id="nomor_opname" name="nomor_opname" type="text" value="{{ old('nomor_opname', $nomorOpname) }}" class="input-field bg-slate-100" readonly placeholder="OPN-20260424-001" required>
                    <p class="hint-text mt-1">Nomor dibuat otomatis oleh sistem.</p>
                </div>
                <div>
                    <label class="label-text" for="tanggal">Tanggal</label>
                    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', now()->format('Y-m-d')) }}" class="input-field" required>
                </div>
                <div class="md:col-span-3">
                    <label class="label-text" for="catatan">Catatan</label>
                    <textarea id="catatan" name="catatan" class="textarea-field" placeholder="Catatan umum stock opname">{{ old('catatan') }}</textarea>
                </div>
            </div>
        </section>

        <section class="surface overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Daftar Barang</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Isi kolom <span class="font-semibold text-slate-700">Stok Fisik</span> untuk barang yang dihitung.
                        Baris yang dikosongkan tidak akan diproses.
                    </p>
                </div>
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" id="opname-search" class="input-field pl-9 w-56" placeholder="Cari nama barang...">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table" id="opname-table">
                    <thead>
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th class="!text-right w-32">Stok Sistem</th>
                            <th class="w-40">Stok Fisik</th>
                            <th class="!text-right w-32">Selisih</th>
                            <th class="w-56">Alasan Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangOptions as $index => $barang)
                            @php
                                $oldRow    = $oldDetail->get($barang['id']);
                                $stokFisik = $oldRow['stok_fisik'] ?? '';
                                $alasan    = $oldRow['alasan'] ?? '';
                            @endphp
                            <tr data-barang-row data-nama="{{ strtolower($barang['nama']) }}">
                                <td>
                                    <input type="hidden" name="detail[{{ $index }}][barang_id]" value="{{ $barang['id'] }}">
                                    <div class="font-semibold text-slate-900">{{ $barang['nama'] }}</div>
                                    <div class="text-xs text-slate-400">{{ $barang['kode'] }}</div>
                                </td>
                                <td class="text-slate-500">{{ $barang['kategori'] }}</td>
                                <td class="text-right font-mono text-slate-700">
                                    {{ $barang['stok'] }} <span class="text-xs text-slate-400">{{ $barang['satuan'] }}</span>
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="detail[{{ $index }}][stok_fisik]"
                                        min="0"
                                        step="1"
                                        value="{{ $stokFisik }}"
                                        class="input-field w-full text-right"
                                        placeholder="—"
                                        data-stok-sistem="{{ $barang['stok'] }}"
                                        data-stok-input="{{ $index }}"
                                    >
                                </td>
                                <td>
                                    <span
                                        class="block text-right font-semibold text-slate-400"
                                        data-selisih-cell="{{ $index }}"
                                    >—</span>
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="detail[{{ $index }}][alasan]"
                                        value="{{ $alasan }}"
                                        class="input-field w-full"
                                        placeholder="Opsional"
                                    >
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">
                <x-ui.icon name="check-circle" class="h-4 w-4" />
                <span>Simpan Stock Opname</span>
            </button>
            <a href="{{ route('inventory.stok-opname.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Live selisih calculation
            document.querySelectorAll('[data-stok-input]').forEach(function (input) {
                const index = input.getAttribute('data-stok-input');
                const stokSistem = parseInt(input.getAttribute('data-stok-sistem'), 10);
                const selisihCell = document.querySelector(`[data-selisih-cell="${index}"]`);

                const update = () => {
                    if (input.value === '' || input.value === null) {
                        selisihCell.textContent = '—';
                        selisihCell.className = 'block text-right font-semibold text-slate-400';
                        return;
                    }
                    const selisih = parseInt(input.value, 10) - stokSistem;
                    selisihCell.textContent = (selisih > 0 ? '+' : '') + selisih;
                    selisihCell.className = 'block text-right font-semibold ' + (
                        selisih === 0 ? 'text-slate-500' :
                        selisih > 0   ? 'text-emerald-700' : 'text-rose-700'
                    );
                };

                input.addEventListener('input', update);
                // Restore selisih on page reload after validation error
                if (input.value !== '') update();
            });

            // Search filter
            const searchInput = document.getElementById('opname-search');
            searchInput?.addEventListener('input', function () {
                const q = this.value.toLowerCase().trim();
                document.querySelectorAll('[data-barang-row]').forEach(row => {
                    const nama = row.getAttribute('data-nama') || '';
                    row.style.display = q === '' || nama.includes(q) ? '' : 'none';
                });
            });
        });
    </script>
@endpush

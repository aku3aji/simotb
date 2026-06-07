@extends('layouts.app')

@section('title', 'Tambah Stock Opname')

@section('content')
    @php
        $detailItems = old('detail', [['barang_id' => '', 'stok_fisik' => '', 'alasan' => '']]);
        $barangOptions = $barangList->map(fn ($item) => [
            'id' => $item->id,
            'kode' => $item->kode_barang,
            'nama' => $item->nama,
            'stok' => (int) $item->stok,
            'kategori' => $item->kategori->nama ?? '-',
            'satuan' => $item->satuan->singkatan ?? $item->satuan->nama ?? '',
        ])->values();
    @endphp

    <x-ui.page-header title="Buat Stock Opname" description="Input hasil hitung fisik dan catat selisih stok secara jelas.">
        <a href="{{ route('inventory.stok-opname.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    @include('partials.form-errors')

    <form method="POST" action="{{ route('inventory.stok-opname.store') }}" class="space-y-6">
        @csrf

        <section class="surface p-6">
            <div class="grid gap-6 md:grid-cols-3">
                <div>
                    <label class="label-text" for="nomor_opname">Nomor Opname</label>
                    <input id="nomor_opname" name="nomor_opname" type="text" value="{{ old('nomor_opname') }}" class="input-field" placeholder="OPN-20260424-001" required>
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
                    <h2 class="text-lg font-bold text-slate-900">Detail Barang</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih barang yang dicek, isi stok fisik, lalu jelaskan selisih jika ada.</p>
                </div>
                <button type="button" class="btn btn-secondary" data-opname-add>
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    <span>Tambah Baris</span>
                </button>
            </div>

            <div class="space-y-4 px-5 py-5" data-opname-rows></div>
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
            const barangOptions = @json($barangOptions);
            const barangMap = Object.fromEntries(barangOptions.map(item => [String(item.id), item]));
            let rows = @json($detailItems);

            const escHtml = (str) => String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');

            const rowsContainer = document.querySelector('[data-opname-rows]');
            const addButton = document.querySelector('[data-opname-add]');

            const render = () => {
                rowsContainer.innerHTML = rows.map((row, index) => {
                    const barang = barangMap[String(row.barang_id ?? '')];
                    const stokSistem = barang ? Number(barang.stok) : 0;
                    const stokFisik = Number(row.stok_fisik || 0);
                    const selisih = barang ? stokFisik - stokSistem : 0;

                    return `
                        <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-4">
                            <div class="grid gap-4 xl:grid-cols-[minmax(0,1.3fr)_180px_180px_180px_auto]">
                                <div>
                                    <label class="label-text">Barang</label>
                                    <select name="detail[${index}][barang_id]" class="select-field" required data-row-select="${index}">
                                        <option value="">Pilih barang</option>
                                        ${barangOptions.map(item => `
                                            <option value="${escHtml(item.id)}" ${String(row.barang_id ?? '') === String(item.id) ? 'selected' : ''}>
                                                ${escHtml(item.nama)} (${escHtml(item.kode)})
                                            </option>
                                        `).join('')}
                                    </select>
                                    <p class="hint-text">${barang ? `${escHtml(barang.kategori)} • Stok sistem ${barang.stok} ${escHtml(barang.satuan)}` : 'Pilih barang untuk melihat stok sistem.'}</p>
                                </div>
                                <div>
                                    <label class="label-text">Stok Sistem</label>
                                    <input type="number" class="input-field bg-slate-100" value="${barang ? stokSistem : ''}" readonly>
                                </div>
                                <div>
                                    <label class="label-text">Stok Fisik</label>
                                    <input type="number" min="0" step="1" name="detail[${index}][stok_fisik]" class="input-field" value="${row.stok_fisik ?? ''}" required data-row-stok="${index}">
                                </div>
                                <div>
                                    <label class="label-text">Selisih</label>
                                    <input type="text" class="input-field ${selisih === 0 ? 'text-slate-500' : (selisih > 0 ? 'text-emerald-700' : 'text-rose-700')}" value="${barang ? selisih : '-'}" readonly>
                                </div>
                                <div class="flex items-end">
                                    <button type="button" class="btn btn-danger px-3 py-2" data-row-remove="${index}" ${rows.length === 1 ? 'disabled' : ''}>
                                        <x-ui.icon name="trash" class="h-4 w-4" />
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="label-text">Alasan Selisih</label>
                                <textarea name="detail[${index}][alasan]" class="textarea-field min-h-[90px]" placeholder="Tulis alasan jika stok fisik berbeda dari sistem">${escHtml(row.alasan)}</textarea>
                            </div>
                        </div>
                    `;
                }).join('');
            };

            rowsContainer.addEventListener('change', function (event) {
                const selectIndex = event.target.getAttribute('data-row-select');
                const stokIndex = event.target.getAttribute('data-row-stok');

                if (selectIndex !== null) {
                    rows[selectIndex].barang_id = event.target.value;
                    render();
                }

                if (stokIndex !== null) {
                    rows[stokIndex].stok_fisik = event.target.value;
                    render();
                }
            });

            rowsContainer.addEventListener('input', function (event) {
                const stokIndex = event.target.getAttribute('data-row-stok');

                if (stokIndex !== null) {
                    rows[stokIndex].stok_fisik = event.target.value;
                    render();
                }
            });

            rowsContainer.addEventListener('click', function (event) {
                const button = event.target.closest('[data-row-remove]');
                if (!button) {
                    return;
                }

                rows.splice(Number(button.getAttribute('data-row-remove')), 1);
                render();
            });

            addButton.addEventListener('click', function () {
                rows.push({ barang_id: '', stok_fisik: '', alasan: '' });
                render();
            });

            render();
        });
    </script>
@endpush

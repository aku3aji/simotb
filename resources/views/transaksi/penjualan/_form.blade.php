@php
    $detailItems = old('detail', isset($penjualan)
        ? $penjualan->detail->map(fn ($item) => [
            'barang_id' => $item->barang_id,
            'jumlah' => $item->jumlah,
            'harga_jual' => $item->harga_jual,
        ])->values()->all()
        : [['barang_id' => '', 'jumlah' => 1, 'harga_jual' => '']]
    );

    $barangOptions = $barangList->map(fn ($item) => [
        'id' => $item->id,
        'kode' => $item->kode_barang,
        'nama' => $item->nama,
        'stok' => (int) $item->stok,
        'harga_jual' => (float) $item->harga_jual,
        'satuan' => $item->satuan->singkatan ?? $item->satuan->nama ?? '',
        'merek' => $item->merek->nama ?? 'Tanpa merek',
    ])->values();
@endphp

<div class="space-y-6">
    <section class="surface p-6">
        <div class="grid gap-6 lg:grid-cols-2 xl:grid-cols-4">
            <div>
                <label class="label-text" for="nomor_penjualan">Nomor Penjualan</label>
                <input id="nomor_penjualan" name="nomor_penjualan" type="text" value="{{ old('nomor_penjualan', $penjualan->nomor_penjualan ?? '') }}" class="input-field" placeholder="TRX-20260424-001" required>
            </div>
            <div>
                <label class="label-text" for="tanggal">Tanggal</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', isset($penjualan->tanggal) ? $penjualan->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" class="input-field" required>
            </div>
            <div class="xl:col-span-2">
                <label class="label-text" for="pelanggan_id">Pelanggan</label>
                <select id="pelanggan_id" name="pelanggan_id" class="select-field">
                    <option value="">Pelanggan Umum</option>
                    @foreach ($pelangganList as $item)
                        <option value="{{ $item->id }}" @selected(old('pelanggan_id', $penjualan->pelanggan_id ?? '') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(360px,0.85fr)]">
        <section class="surface overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Item Penjualan</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih barang, atur kuantitas, dan sesuaikan harga jual jika diperlukan.</p>
                </div>
                <button type="button" class="btn btn-secondary" data-penjualan-add>
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    <span>Tambah Item</span>
                </button>
            </div>

            <div class="space-y-4 px-5 py-5" data-penjualan-rows></div>
        </section>

        <aside class="surface p-6 xl:sticky xl:top-24 xl:self-start">
            <h3 class="text-2xl font-extrabold text-slate-900">Ringkasan Pembayaran</h3>

            <div class="mt-6">
                <p class="label-text">Metode Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer rounded-md border px-4 py-3 text-center text-sm font-semibold transition has-[:checked]:border-brand-700 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                        <input type="radio" name="tipe_pembayaran" value="tunai" class="sr-only" {{ old('tipe_pembayaran', $penjualan->tipe_pembayaran ?? 'tunai') === 'tunai' ? 'checked' : '' }}>
                        Tunai
                    </label>
                    <label class="cursor-pointer rounded-md border px-4 py-3 text-center text-sm font-semibold transition has-[:checked]:border-brand-700 has-[:checked]:bg-brand-50 has-[:checked]:text-brand-700">
                        <input type="radio" name="tipe_pembayaran" value="kredit" class="sr-only" {{ old('tipe_pembayaran', $penjualan->tipe_pembayaran ?? '') === 'kredit' ? 'checked' : '' }}>
                        Kredit
                    </label>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="summary-item">
                    <span class="text-slate-500">Subtotal</span>
                    <span class="font-semibold text-slate-900" data-penjualan-subtotal>Rp 0</span>
                </div>
                <div class="summary-item">
                    <span class="text-slate-500">Jumlah Item</span>
                    <span class="font-semibold text-slate-900" data-penjualan-item-count>0</span>
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-brand-200 bg-brand-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-700">Total Tagihan</p>
                <p class="mt-3 text-4xl font-extrabold text-brand-800" data-penjualan-total>Rp 0</p>
            </div>

            <div class="mt-6 space-y-4">
                <div>
                    <label class="label-text" for="dibayar">Jumlah Dibayar</label>
                    <input id="dibayar" name="dibayar" type="number" min="0" step="0.01" value="{{ old('dibayar', $penjualan->dibayar ?? 0) }}" class="input-field" data-penjualan-dibayar required>
                </div>
                <div data-penjualan-jatuh-tempo-wrap>
                    <label class="label-text" for="jatuh_tempo">Jatuh Tempo</label>
                    <input id="jatuh_tempo" name="jatuh_tempo" type="date" value="{{ old('jatuh_tempo', isset($penjualan->jatuh_tempo) ? $penjualan->jatuh_tempo->format('Y-m-d') : '') }}" class="input-field" data-penjualan-jatuh-tempo>
                </div>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3">
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-emerald-700">Sisa Piutang</p>
                    <p class="mt-2 text-2xl font-extrabold text-emerald-800" data-penjualan-sisa>Rp 0</p>
                </div>
                <div>
                    <label class="label-text" for="catatan">Catatan</label>
                    <textarea id="catatan" name="catatan" class="textarea-field" placeholder="Catatan transaksi">{{ old('catatan', $penjualan->catatan ?? '') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="btn btn-success w-full justify-center">{{ $submitLabel }}</button>
                <a href="{{ route('transaksi.penjualan.index') }}" class="btn btn-secondary w-full justify-center">Batal</a>
            </div>
        </aside>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const barangOptions = @json($barangOptions);
                let rows = @json($detailItems);

                const rowsEl = document.querySelector('[data-penjualan-rows]');
                const addButton = document.querySelector('[data-penjualan-add]');
                const subtotalEl = document.querySelector('[data-penjualan-subtotal]');
                const totalEl = document.querySelector('[data-penjualan-total]');
                const sisaEl = document.querySelector('[data-penjualan-sisa]');
                const countEl = document.querySelector('[data-penjualan-item-count]');
                const dibayarInput = document.querySelector('[data-penjualan-dibayar]');
                const jatuhTempoWrap = document.querySelector('[data-penjualan-jatuh-tempo-wrap]');
                const jatuhTempoInput = document.querySelector('[data-penjualan-jatuh-tempo]');

                const formatRupiah = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(Number(value || 0))}`;

                const currentType = () => document.querySelector('input[name="tipe_pembayaran"]:checked')?.value || 'tunai';

                const syncSummary = () => {
                    const total = rows.reduce((sum, row) => sum + (Number(row.jumlah || 0) * Number(row.harga_jual || 0)), 0);
                    const jumlahItem = rows.reduce((sum, row) => sum + Number(row.jumlah || 0), 0);

                    if (currentType() === 'tunai') {
                        dibayarInput.value = total;
                        dibayarInput.readOnly = true;
                        jatuhTempoWrap.classList.add('hidden');
                        jatuhTempoInput.value = '';
                    } else {
                        dibayarInput.readOnly = false;
                        jatuhTempoWrap.classList.remove('hidden');
                    }

                    const dibayar = Number(dibayarInput.value || 0);
                    const sisa = Math.max(total - dibayar, 0);

                    subtotalEl.textContent = formatRupiah(total);
                    totalEl.textContent = formatRupiah(total);
                    sisaEl.textContent = formatRupiah(sisa);
                    countEl.textContent = `${jumlahItem} qty`;
                };

                const render = () => {
                    rowsEl.innerHTML = rows.map((row, index) => {
                        const selected = barangOptions.find(item => String(item.id) === String(row.barang_id ?? ''));
                        const jumlah = Number(row.jumlah || 0);
                        const harga = Number(row.harga_jual || (selected ? selected.harga_jual : 0));
                        const subtotal = jumlah * harga;

                        return `
                            <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-4">
                                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_120px_180px_180px_auto]">
                                    <div>
                                        <label class="label-text">Barang</label>
                                        <select name="detail[${index}][barang_id]" class="select-field" data-penjualan-field="barang_id" data-index="${index}" required>
                                            <option value="">Pilih barang</option>
                                            ${barangOptions.map(item => `
                                                <option value="${item.id}" ${String(row.barang_id ?? '') === String(item.id) ? 'selected' : ''}>
                                                    ${item.nama} (${item.kode})
                                                </option>
                                            `).join('')}
                                        </select>
                                        <p class="hint-text">${selected ? `${selected.merek} • Stok ${selected.stok} ${selected.satuan}` : 'Pilih barang untuk melihat stok tersedia.'}</p>
                                    </div>
                                    <div>
                                        <label class="label-text">Qty</label>
                                        <input type="number" min="1" step="1" name="detail[${index}][jumlah]" value="${row.jumlah ?? 1}" class="input-field" data-penjualan-field="jumlah" data-index="${index}" required>
                                    </div>
                                    <div>
                                        <label class="label-text">Harga Jual</label>
                                        <input type="number" min="0.01" step="0.01" name="detail[${index}][harga_jual]" value="${row.harga_jual ?? (selected ? selected.harga_jual : '')}" class="input-field" data-penjualan-field="harga_jual" data-index="${index}" required>
                                    </div>
                                    <div>
                                        <label class="label-text">Subtotal</label>
                                        <input type="text" value="${formatRupiah(subtotal)}" class="input-field bg-slate-100 font-semibold text-slate-900" readonly data-penjualan-row-subtotal="${index}">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="btn btn-danger px-3 py-2" data-penjualan-remove="${index}" ${rows.length === 1 ? 'disabled' : ''}>
                                            <x-ui.icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                    syncSummary();
                };

                rowsEl.addEventListener('input', function (event) {
                    const field = event.target.getAttribute('data-penjualan-field');
                    const index = event.target.getAttribute('data-index');

                    if (field && index !== null) {
                        rows[index][field] = event.target.value;

                        if (field === 'jumlah' || field === 'harga_jual') {
                            const row = rows[index];
                            const subtotal = Number(row.jumlah || 0) * Number(row.harga_jual || 0);
                            const subtotalEl = rowsEl.querySelector(`[data-penjualan-row-subtotal="${index}"]`);
                            if (subtotalEl) subtotalEl.value = formatRupiah(subtotal);
                        }

                        syncSummary();
                    }
                });

                rowsEl.addEventListener('change', function (event) {
                    const field = event.target.getAttribute('data-penjualan-field');
                    const index = event.target.getAttribute('data-index');

                    if (field && index !== null) {
                        rows[index][field] = event.target.value;

                        if (field === 'barang_id') {
                            const selected = barangOptions.find(item => String(item.id) === String(event.target.value));
                            if (selected) {
                                rows[index].harga_jual = selected.harga_jual;
                            }
                            render();
                            return;
                        }

                        syncSummary();
                    }
                });

                rowsEl.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-penjualan-remove]');
                    if (!button) {
                        return;
                    }

                    rows.splice(Number(button.getAttribute('data-penjualan-remove')), 1);
                    render();
                });

                addButton.addEventListener('click', function () {
                    rows.push({ barang_id: '', jumlah: 1, harga_jual: '' });
                    render();
                });

                document.querySelectorAll('input[name="tipe_pembayaran"]').forEach((radio) => {
                    radio.addEventListener('change', syncSummary);
                });

                dibayarInput.addEventListener('input', syncSummary);

                render();
            });
        </script>
    @endpush
@endonce

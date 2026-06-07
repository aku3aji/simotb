@php
    $detailItems = old('detail', isset($returPenjualan)
        ? $returPenjualan->detail->map(fn ($item) => [
            'barang_id' => $item->barang_id,
            'jumlah' => $item->jumlah,
            'harga_jual' => $item->harga_jual,
            'kondisi_barang' => $item->kondisi_barang,
        ])->values()->all()
        : [['barang_id' => '', 'jumlah' => 1, 'harga_jual' => '', 'kondisi_barang' => 'baik']]
    );

    $penjualanOptions = $penjualanList->map(fn ($item) => [
        'id' => $item->id,
        'nomor' => $item->nomor_penjualan,
        'pelanggan' => $item->pelanggan->nama ?? 'Pelanggan Umum',
        'tanggal' => optional($item->tanggal)->format('Y-m-d'),
        'detail' => $item->detail->map(fn ($detail) => [
            'barang_id' => $detail->barang_id,
            'nama' => $detail->barang->nama ?? 'Barang',
            'kode' => $detail->barang->kode_barang ?? '-',
            'jumlah' => (int) $detail->jumlah,
            'harga_jual' => (float) $detail->harga_jual,
        ])->values(),
    ])->values();
@endphp

<div class="space-y-6">
    <section class="surface p-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <div>
                <label class="label-text" for="nomor_retur">Nomor Retur</label>
                <input id="nomor_retur" name="nomor_retur" type="text" value="{{ old('nomor_retur', $returPenjualan->nomor_retur ?? '') }}" class="input-field" placeholder="RTR-20260424-001" required>
            </div>
            <div>
                <label class="label-text" for="penjualan_id">Transaksi Penjualan</label>
                <select id="penjualan_id" name="penjualan_id" class="select-field" required data-retur-penjualan>
                    <option value="">Pilih transaksi penjualan</option>
                    @foreach ($penjualanList as $item)
                        <option value="{{ $item->id }}" @selected(old('penjualan_id', $returPenjualan->penjualan_id ?? '') == $item->id)>
                            {{ $item->nomor_penjualan }} - {{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="tanggal">Tanggal Retur</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', isset($returPenjualan->tanggal) ? $returPenjualan->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" class="input-field" required>
            </div>
            <div class="lg:col-span-3">
                <label class="label-text" for="alasan">Alasan Retur</label>
                <textarea id="alasan" name="alasan" class="textarea-field" placeholder="Tulis alasan pengembalian barang">{{ old('alasan', $returPenjualan->alasan ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.55fr)_minmax(340px,0.85fr)]">
        <section class="surface overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Detail Retur</h2>
                    <p class="mt-1 text-sm text-slate-500">Pilih barang dari transaksi penjualan, isi jumlah retur, dan kondisi barang.</p>
                </div>
                <button type="button" class="btn btn-secondary" data-retur-add>
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    <span>Tambah Item</span>
                </button>
            </div>

            <div class="space-y-4 px-5 py-5" data-retur-rows></div>
        </section>

        <aside class="space-y-6">
            <section class="surface p-6">
                <h3 class="text-lg font-bold text-slate-900">Info Transaksi</h3>
                <div class="mt-4 space-y-3 text-sm" data-retur-summary>
                    <div class="summary-item">
                        <span class="text-slate-500">Pelanggan</span>
                        <span class="font-semibold text-slate-900">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="text-slate-500">Tanggal Nota</span>
                        <span class="font-semibold text-slate-900">-</span>
                    </div>
                    <div class="summary-item">
                        <span class="text-slate-500">Nomor Nota</span>
                        <span class="font-semibold text-slate-900">-</span>
                    </div>
                </div>
            </section>

            <section class="surface p-6">
                <h3 class="text-lg font-bold text-slate-900">Total Retur</h3>
                <p class="mt-3 text-4xl font-extrabold text-brand-800" data-retur-total>Rp 0</p>
                <p class="mt-2 text-sm text-slate-500">Barang berkondisi baik akan menambah stok kembali ke sistem.</p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <button type="submit" class="btn btn-primary w-full justify-center">{{ $submitLabel }}</button>
                    <a href="{{ route('transaksi.retur-penjualan.index') }}" class="btn btn-secondary w-full justify-center">Batal</a>
                </div>
            </section>
        </aside>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const transaksiOptions = @json($penjualanOptions);
                const transaksiSelect = document.querySelector('[data-retur-penjualan]');
                const rowsEl = document.querySelector('[data-retur-rows]');
                const addButton = document.querySelector('[data-retur-add]');
                const totalEl = document.querySelector('[data-retur-total]');
                const summaryEl = document.querySelector('[data-retur-summary]');
                let rows = @json($detailItems);

                const formatRupiah = (value) => `Rp ${new Intl.NumberFormat('id-ID').format(Number(value || 0))}`;

                const selectedTransaksi = () => transaksiOptions.find(item => String(item.id) === String(transaksiSelect.value));

                const availableItems = () => selectedTransaksi()?.detail ?? [];

                const renderSummary = () => {
                    const transaksi = selectedTransaksi();

                    if (!transaksi) {
                        summaryEl.innerHTML = `
                            <div class="summary-item"><span class="text-slate-500">Pelanggan</span><span class="font-semibold text-slate-900">-</span></div>
                            <div class="summary-item"><span class="text-slate-500">Tanggal Nota</span><span class="font-semibold text-slate-900">-</span></div>
                            <div class="summary-item"><span class="text-slate-500">Nomor Nota</span><span class="font-semibold text-slate-900">-</span></div>
                        `;
                        return;
                    }

                    summaryEl.innerHTML = `
                        <div class="summary-item"><span class="text-slate-500">Pelanggan</span><span class="font-semibold text-slate-900">${transaksi.pelanggan}</span></div>
                        <div class="summary-item"><span class="text-slate-500">Tanggal Nota</span><span class="font-semibold text-slate-900">${transaksi.tanggal}</span></div>
                        <div class="summary-item"><span class="text-slate-500">Nomor Nota</span><span class="font-semibold text-slate-900">${transaksi.nomor}</span></div>
                    `;
                };

                const render = () => {
                    const items = availableItems();
                    const total = rows.reduce((sum, row) => sum + (Number(row.jumlah || 0) * Number(row.harga_jual || 0)), 0);
                    totalEl.textContent = formatRupiah(total);
                    renderSummary();

                    rowsEl.innerHTML = rows.map((row, index) => {
                        const selected = items.find(item => String(item.barang_id) === String(row.barang_id ?? ''));
                        const subtotal = Number(row.jumlah || 0) * Number(row.harga_jual || 0);

                        return `
                            <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-4">
                                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.3fr)_120px_180px_180px_auto]">
                                    <div>
                                        <label class="label-text">Barang</label>
                                        <select name="detail[${index}][barang_id]" class="select-field" data-retur-field="barang_id" data-index="${index}" required>
                                            <option value="">Pilih barang</option>
                                            ${items.map(item => `
                                                <option value="${item.barang_id}" ${String(row.barang_id ?? '') === String(item.barang_id) ? 'selected' : ''}>
                                                    ${item.nama} (${item.kode})
                                                </option>
                                            `).join('')}
                                        </select>
                                        <p class="hint-text">${selected ? `Terjual ${selected.jumlah} • Harga jual ${formatRupiah(selected.harga_jual)}` : 'Pilih transaksi penjualan terlebih dahulu.'}</p>
                                    </div>
                                    <div>
                                        <label class="label-text">Jumlah</label>
                                        <input type="number" min="1" step="1" ${selected ? `max="${selected.jumlah}"` : ''} name="detail[${index}][jumlah]" value="${row.jumlah ?? 1}" class="input-field" data-retur-field="jumlah" data-index="${index}" required>
                                        ${selected ? `<p class="hint-text">Maks. ${selected.jumlah}</p>` : ''}
                                    </div>
                                    <div>
                                        <label class="label-text">Harga Jual</label>
                                        <input type="number" min="0" step="0.01" name="detail[${index}][harga_jual]" value="${row.harga_jual ?? (selected ? selected.harga_jual : '')}" class="input-field" data-retur-field="harga_jual" data-index="${index}" required>
                                    </div>
                                    <div>
                                        <label class="label-text">Kondisi</label>
                                        <select name="detail[${index}][kondisi_barang]" class="select-field" data-retur-field="kondisi_barang" data-index="${index}" required>
                                            <option value="baik" ${row.kondisi_barang === 'baik' ? 'selected' : ''}>Baik</option>
                                            <option value="rusak" ${row.kondisi_barang === 'rusak' ? 'selected' : ''}>Rusak</option>
                                        </select>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="btn btn-danger px-3 py-2" data-retur-remove="${index}" ${rows.length === 1 ? 'disabled' : ''}>
                                            <x-ui.icon name="trash" class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-4 rounded-md border border-slate-200 bg-white px-4 py-3 text-sm">
                                    <div class="flex items-center justify-between">
                                        <span class="text-slate-500">Subtotal retur</span>
                                        <span class="font-semibold text-slate-900">${formatRupiah(subtotal)}</span>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                };

                rowsEl.addEventListener('input', function (event) {
                    const field = event.target.getAttribute('data-retur-field');
                    const index = event.target.getAttribute('data-index');

                    if (field && index !== null) {
                        rows[index][field] = event.target.value;
                        render();
                    }
                });

                rowsEl.addEventListener('change', function (event) {
                    const field = event.target.getAttribute('data-retur-field');
                    const index = event.target.getAttribute('data-index');

                    if (field && index !== null) {
                        rows[index][field] = event.target.value;

                        if (field === 'barang_id') {
                            const selected = availableItems().find(item => String(item.barang_id) === String(event.target.value));
                            if (selected) {
                                rows[index].harga_jual = selected.harga_jual;
                            }
                        }

                        render();
                    }
                });

                rowsEl.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-retur-remove]');
                    if (!button) {
                        return;
                    }

                    rows.splice(Number(button.getAttribute('data-retur-remove')), 1);
                    render();
                });

                addButton.addEventListener('click', function () {
                    rows.push({ barang_id: '', jumlah: 1, harga_jual: '', kondisi_barang: 'baik' });
                    render();
                });

                transaksiSelect.addEventListener('change', function () {
                    rows = [{ barang_id: '', jumlah: 1, harga_jual: '', kondisi_barang: 'baik' }];
                    render();
                });

                render();
            });
        </script>
    @endpush
@endonce

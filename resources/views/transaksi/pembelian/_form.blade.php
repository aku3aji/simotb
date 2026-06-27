@php
    $isEdit = isset($pembelian);

    $detailItems = old('detail', $isEdit
        ? $pembelian->detail->map(fn ($item) => [
            'barang_id'        => $item->barang_id,
            'jumlah'           => $item->jumlah,
            'harga_beli'       => $item->harga_beli,
            'barang_nama_baru' => '',
        ])->values()->all()
        : [['barang_id' => '', 'jumlah' => 1, 'harga_beli' => '', 'barang_nama_baru' => '']]
    );

    $barangOptions = $barangList->map(fn ($item) => [
        'id'         => $item->id,
        'kode'       => $item->kode_barang,
        'nama'       => $item->nama,
        'stok'       => (int) $item->stok,
        'harga_beli' => (float) $item->harga_beli,
        'satuan'     => $item->satuan->singkatan ?? $item->satuan->nama ?? '',
    ])->values();

    $nomorValue   = old('nomor_pembelian', $isEdit ? $pembelian->nomor_pembelian : ($nomorPembelian ?? ''));
    $vendorSelected = old('vendor_id', $pembelian->vendor_id ?? '');
@endphp

<div class="space-y-6">
    <section class="surface p-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <div>
                <label class="label-text" for="nomor_pembelian">Nomor Pembelian</label>
                <input id="nomor_pembelian" name="nomor_pembelian" type="text"
                    value="{{ $nomorValue }}"
                    class="input-field {{ !$isEdit ? 'bg-slate-100' : '' }}"
                    {{ !$isEdit ? 'readonly' : '' }}
                    required>
                @if (!$isEdit)
                    <p class="hint-text mt-1">Nomor dibuat otomatis oleh sistem.</p>
                @endif
            </div>
            <div>
                <label class="label-text" for="vendor_id">Vendor</label>
                <select id="vendor_id" name="vendor_id" class="select-field" required>
                    <option value="">Pilih vendor</option>
                    @foreach ($vendorList as $item)
                        <option value="{{ $item->id }}" @selected($vendorSelected == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                    <option value="__new__" @selected($vendorSelected === '__new__')>+ Tambah vendor baru...</option>
                </select>
                <div id="vendorBaruWrap" class="{{ old('vendor_id') === '__new__' ? '' : 'hidden' }} mt-2">
                    <input type="text" name="vendor_nama_baru"
                        value="{{ old('vendor_nama_baru') }}"
                        class="input-field"
                        placeholder="Masukkan nama vendor baru">
                </div>
            </div>
            <div>
                <label class="label-text" for="tanggal">Tanggal</label>
                <input id="tanggal" name="tanggal" type="date"
                    value="{{ old('tanggal', isset($pembelian->tanggal) ? $pembelian->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}"
                    class="input-field" required>
            </div>
            <div class="lg:col-span-3">
                <label class="label-text" for="catatan">Catatan</label>
                <textarea id="catatan" name="catatan" class="textarea-field" placeholder="Catatan tambahan pembelian">{{ old('catatan', $pembelian->catatan ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <div class="grid gap-6 2xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,0.8fr)]">
        <section class="surface">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-900">Item Pembelian</h2>
                    <p class="mt-1 text-sm text-slate-500">Tambahkan barang yang dibeli dari vendor, lalu isi jumlah dan harga beli.</p>
                </div>
                <button type="button" class="btn btn-secondary" data-pembelian-add>
                    <x-ui.icon name="plus" class="h-4 w-4" />
                    <span>Tambah Barang</span>
                </button>
            </div>

            <div class="space-y-4 px-5 py-5" data-pembelian-rows></div>
        </section>

        <aside class="surface p-6">
            <h3 class="text-lg font-bold text-slate-900">Ringkasan Pembelian</h3>
            <p class="mt-2 text-sm text-slate-500">Total dihitung otomatis dari seluruh detail barang yang Anda masukkan.</p>

            <div class="mt-6 rounded-lg border border-brand-200 bg-brand-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand-700">Total Pembelian</p>
                <p class="mt-3 text-3xl font-extrabold text-brand-800" data-pembelian-total>Rp 0</p>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                <a href="{{ route('transaksi.pembelian.index') }}" class="btn btn-secondary">Batal</a>
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
                const totalEl = document.querySelector('[data-pembelian-total]');
                const rowsEl = document.querySelector('[data-pembelian-rows]');
                const addButton = document.querySelector('[data-pembelian-add]');

                // Vendor inline
                const vendorSelect = document.getElementById('vendor_id');
                const vendorBaruWrap = document.getElementById('vendorBaruWrap');
                vendorSelect?.addEventListener('change', function () {
                    vendorBaruWrap.classList.toggle('hidden', this.value !== '__new__');
                });

                const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

                const initTomSelects = () => {
                    rowsEl.querySelectorAll('select[data-pembelian-field="barang_id"]').forEach(sel => {
                        if (sel.tomselect) return;
                        new TomSelect(sel, {
                            plugins: { dropdown_input: {} },
                            maxOptions: null,
                            highlight: true,
                            placeholder: 'Pilih barang',
                        });
                    });
                };

                const render = () => {
                    const total = rows.reduce((sum, row) => sum + (Number(row.jumlah || 0) * Number(row.harga_beli || 0)), 0);
                    totalEl.textContent = `Rp ${formatRupiah(total)}`;

                    rowsEl.innerHTML = rows.map((row, index) => {
                        const isNew = String(row.barang_id ?? '') === '__new__';
                        const selected = isNew ? null : barangOptions.find(item => String(item.id) === String(row.barang_id ?? ''));
                        const subtotal = Number(row.jumlah || 0) * Number(row.harga_beli || 0);

                        const barangSelectOptions = barangOptions.map(item => `
                            <option value="${item.id}" ${String(row.barang_id ?? '') === String(item.id) ? 'selected' : ''}>
                                ${item.nama} (${item.kode})
                            </option>
                        `).join('') + `<option value="__new__" ${isNew ? 'selected' : ''}>+ Tambah barang baru...</option>`;

                        const barangNamaBaruInput = isNew ? `
                            <input type="text"
                                name="detail[${index}][barang_nama_baru]"
                                value="${(row.barang_nama_baru || '').replace(/"/g, '&quot;')}"
                                class="input-field mt-2"
                                placeholder="Nama barang baru"
                                data-pembelian-field="barang_nama_baru"
                                data-index="${index}">
                        ` : `<input type="hidden" name="detail[${index}][barang_nama_baru]" value="">`;

                        const hintText = isNew
                            ? '<p class="hint-text text-amber-600">Isi nama barang baru, harga beli wajib diisi manual.</p>'
                            : `<p class="hint-text">${selected ? `Stok saat ini ${selected.stok} ${selected.satuan}` : 'Pilih barang untuk melihat stok saat ini.'}</p>`;

                        return `
                            <div class="rounded-lg border border-slate-200 bg-slate-50/70 p-4">
                                <div class="grid gap-4 xl:grid-cols-[minmax(0,1.35fr)_140px_180px_180px_auto] 2xl:grid-cols-[minmax(0,1.35fr)_96px_140px_140px_auto]">
                                    <div>
                                        <label class="label-text">Barang</label>
                                        <select name="detail[${index}][barang_id]" class="select-field" data-pembelian-field="barang_id" data-index="${index}" required>
                                            <option value="">Pilih barang</option>
                                            ${barangSelectOptions}
                                        </select>
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="label-text">Jumlah</label>
                                        <input type="number" min="0.00" step="1" name="detail[${index}][jumlah]" value="${row.jumlah ?? 1}" class="input-field flex-1" data-pembelian-field="jumlah" data-index="${index}" required>
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="label-text">Harga Beli</label>
                                        <input type="number" min="0.00" step="500" name="detail[${index}][harga_beli]" value="${row.harga_beli ?? (selected ? selected.harga_beli : '')}" class="input-field flex-1" data-pembelian-field="harga_beli" data-index="${index}" required>
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="label-text">Subtotal</label>
                                        <input type="text" value="Rp ${formatRupiah(subtotal)}" class="input-field flex-1 bg-slate-100 font-semibold text-slate-900" readonly data-pembelian-subtotal="${index}">
                                    </div>
                                    <div class="flex flex-col">
                                        <label class="label-text invisible">Del</label>
                                        <button type="button" class="btn btn-danger flex-1 px-3" data-pembelian-remove="${index}" ${rows.length === 1 ? 'disabled' : ''}>
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="m19 6-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                        </button>
                                    </div>
                                </div>
                                ${barangNamaBaruInput}
                                ${hintText}
                            </div>
                        `;
                    }).join('');

                    initTomSelects();
                };

                rowsEl.addEventListener('input', function (event) {
                    const field = event.target.getAttribute('data-pembelian-field');
                    const index = parseInt(event.target.getAttribute('data-index'));
                    if (!field || isNaN(index)) return;

                    rows[index][field] = event.target.value;

                    if (field === 'jumlah' || field === 'harga_beli') {
                        const row = rows[index];
                        const subtotal = Number(row.jumlah || 0) * Number(row.harga_beli || 0);
                        const subtotalEl = rowsEl.querySelector(`[data-pembelian-subtotal="${index}"]`);
                        if (subtotalEl) subtotalEl.value = `Rp ${formatRupiah(subtotal)}`;
                        const total = rows.reduce((sum, r) => sum + (Number(r.jumlah || 0) * Number(r.harga_beli || 0)), 0);
                        totalEl.textContent = `Rp ${formatRupiah(total)}`;
                    }
                });

                rowsEl.addEventListener('change', function (event) {
                    const field = event.target.getAttribute('data-pembelian-field');
                    const index = event.target.getAttribute('data-index');
                    if (field && index !== null) {
                        rows[index][field] = event.target.value;

                        if (field === 'barang_id') {
                            if (event.target.value === '__new__') {
                                rows[index].harga_beli = '';
                                rows[index].barang_nama_baru = '';
                            } else {
                                const selected = barangOptions.find(item => String(item.id) === String(event.target.value));
                                if (selected) rows[index].harga_beli = selected.harga_beli;
                                rows[index].barang_nama_baru = '';
                            }
                            requestAnimationFrame(render);
                            return;
                        }

                        render();
                    }
                });

                rowsEl.addEventListener('keydown', function (event) {
                    const field = event.target.getAttribute('data-pembelian-field');
                    if ((field === 'jumlah' || field === 'harga_beli') && ['-', 'e', 'E', '+'].includes(event.key)) {
                        event.preventDefault();
                    }
                });

                rowsEl.addEventListener('click', function (event) {
                    const button = event.target.closest('[data-pembelian-remove]');
                    if (!button) return;
                    rows.splice(Number(button.getAttribute('data-pembelian-remove')), 1);
                    render();
                });

                addButton.addEventListener('click', function () {
                    rows.push({ barang_id: '', jumlah: 1, harga_beli: '', barang_nama_baru: '' });
                    render();
                });

                render();
            });
        </script>
    @endpush
@endonce

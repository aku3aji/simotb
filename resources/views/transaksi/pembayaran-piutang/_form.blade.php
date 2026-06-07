@php
    $penjualanOptions = $penjualanList->map(fn ($item) => [
        'id' => $item->id,
        'nomor' => $item->nomor_penjualan,
        'pelanggan' => $item->pelanggan->nama ?? 'Pelanggan Umum',
        'tanggal' => optional($item->tanggal)->format('Y-m-d'),
        'total' => (float) $item->total,
        'dibayar' => (float) $item->dibayar,
        'sisa_piutang' => (float) $item->sisa_piutang,
    ])->values();
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(320px,0.8fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="label-text" for="penjualan_id">Transaksi Kredit</label>
                <select id="penjualan_id" name="penjualan_id" class="select-field" required data-piutang-select>
                    <option value="">Pilih transaksi kredit</option>
                    @foreach ($penjualanList as $item)
                        <option value="{{ $item->id }}" @selected(old('penjualan_id', $selectedPenjualanId ?? $pembayaranPiutang->penjualan_id ?? '') == $item->id)>
                            {{ $item->nomor_penjualan }} - {{ $item->pelanggan->nama ?? 'Pelanggan Umum' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="tanggal">Tanggal Pembayaran</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', isset($pembayaranPiutang->tanggal) ? $pembayaranPiutang->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" class="input-field" required>
            </div>
            <div>
                <label class="label-text" for="jumlah_bayar">Jumlah Bayar</label>
                <input id="jumlah_bayar" name="jumlah_bayar" type="number" min="0.01" step="0.01" value="{{ old('jumlah_bayar', $pembayaranPiutang->jumlah_bayar ?? 0) }}" class="input-field" required data-piutang-bayar>
            </div>
            <div>
                <label class="label-text" for="metode_pembayaran">Metode Pembayaran</label>
                <input id="metode_pembayaran" name="metode_pembayaran" type="text" value="{{ old('metode_pembayaran', $pembayaranPiutang->metode_pembayaran ?? '') }}" class="input-field" placeholder="Transfer, Tunai, QRIS">
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="catatan">Catatan</label>
                <textarea id="catatan" name="catatan" class="textarea-field" placeholder="Catatan tambahan pembayaran">{{ old('catatan', $pembayaranPiutang->catatan ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Ringkasan Piutang</h3>
        <div class="mt-4 space-y-3 text-sm" data-piutang-summary>
            <div class="summary-item">
                <span class="text-slate-500">Pelanggan</span>
                <span class="font-semibold text-slate-900">-</span>
            </div>
            <div class="summary-item">
                <span class="text-slate-500">Total Nota</span>
                <span class="font-semibold text-slate-900">Rp 0</span>
            </div>
            <div class="summary-item">
                <span class="text-slate-500">Sudah Dibayar</span>
                <span class="font-semibold text-slate-900">Rp 0</span>
            </div>
            <div class="summary-item">
                <span class="text-slate-500">Sisa Piutang</span>
                <span class="font-semibold text-rose-700">Rp 0</span>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('transaksi.pembayaran-piutang.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const penjualanOptions = @json($penjualanOptions);
                const summary = document.querySelector('[data-piutang-summary]');
                const select = document.querySelector('[data-piutang-select]');

                const formatRupiah = (value) => new Intl.NumberFormat('id-ID').format(Number(value || 0));

                const bayarInput = document.querySelector('[data-piutang-bayar]');

                const renderSummary = () => {
                    const selected = penjualanOptions.find(item => String(item.id) === String(select.value));
                    const jumlahBayar = Number(bayarInput?.value || 0);

                    if (!selected) {
                        summary.innerHTML = `
                            <div class="summary-item"><span class="text-slate-500">Pelanggan</span><span class="font-semibold text-slate-900">-</span></div>
                            <div class="summary-item"><span class="text-slate-500">Total Nota</span><span class="font-semibold text-slate-900">Rp 0</span></div>
                            <div class="summary-item"><span class="text-slate-500">Sudah Dibayar</span><span class="font-semibold text-slate-900">Rp 0</span></div>
                            <div class="summary-item"><span class="text-slate-500">Sisa Piutang</span><span class="font-semibold text-rose-700">Rp 0</span></div>
                        `;
                        return;
                    }

                    const sisaSetelahBayar = Math.max(selected.sisa_piutang - jumlahBayar, 0);

                    summary.innerHTML = `
                        <div class="summary-item"><span class="text-slate-500">Pelanggan</span><span class="font-semibold text-slate-900">${selected.pelanggan}</span></div>
                        <div class="summary-item"><span class="text-slate-500">Total Nota</span><span class="font-semibold text-slate-900">Rp ${formatRupiah(selected.total)}</span></div>
                        <div class="summary-item"><span class="text-slate-500">Sudah Dibayar</span><span class="font-semibold text-slate-900">Rp ${formatRupiah(selected.dibayar)}</span></div>
                        <div class="summary-item"><span class="text-slate-500">Sisa Piutang</span><span class="font-semibold text-rose-700">Rp ${formatRupiah(selected.sisa_piutang)}</span></div>
                        <div class="summary-item border-t border-slate-200 pt-3"><span class="font-semibold text-slate-700">Sisa Setelah Bayar</span><span class="font-bold text-emerald-700">Rp ${formatRupiah(sisaSetelahBayar)}</span></div>
                    `;
                };

                select?.addEventListener('change', renderSummary);
                bayarInput?.addEventListener('input', renderSummary);
                renderSummary();
            });
        </script>
    @endpush
@endonce

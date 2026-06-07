<div class="grid gap-6 lg:grid-cols-[minmax(0,1.2fr)_minmax(280px,0.8fr)]">
    <section class="surface p-6">
        <div>
            <label class="label-text" for="nama">Nama Merek</label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $merek->nama ?? '') }}" class="input-field" placeholder="Contoh: Tiga Roda" required>
        </div>

        <div class="mt-6">
            <label class="label-text" for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" class="textarea-field" placeholder="Catatan tambahan tentang merek">{{ old('deskripsi', $merek->deskripsi ?? '') }}</textarea>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Ringkasan</h3>
        <p class="mt-2 text-sm text-slate-500">Merek membantu owner membaca pola penjualan antar brand dan stok produk populer.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('master-data.merek.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

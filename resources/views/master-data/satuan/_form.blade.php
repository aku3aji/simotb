<div class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(280px,0.9fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="label-text" for="nama">Nama Satuan</label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $satuan->nama ?? '') }}" class="input-field" placeholder="Contoh: Sak" required>
            </div>
            <div>
                <label class="label-text" for="singkatan">Singkatan</label>
                <input id="singkatan" name="singkatan" type="text" value="{{ old('singkatan', $satuan->singkatan ?? '') }}" class="input-field" placeholder="Contoh: sk">
            </div>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Tips</h3>
        <p class="mt-2 text-sm text-slate-500">Gunakan satuan yang mudah dipahami admin seperti sak, pcs, batang, atau dus.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('master-data.satuan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

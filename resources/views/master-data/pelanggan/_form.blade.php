<div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.9fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="label-text" for="nama">Nama Pelanggan</label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $pelanggan->nama ?? '') }}" class="input-field" placeholder="Contoh: Bpk. Budi Santoso" required>
            </div>
            <div>
                <label class="label-text" for="telepon">Telepon</label>
                <input id="telepon" name="telepon" type="text" value="{{ old('telepon', $pelanggan->telepon ?? '') }}" class="input-field" placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="label-text" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $pelanggan->email ?? '') }}" class="input-field" placeholder="pelanggan@email.com">
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="textarea-field" placeholder="Alamat pelanggan">{{ old('alamat', $pelanggan->alamat ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Catatan</h3>
        <p class="mt-2 text-sm text-slate-500">Data pelanggan sangat membantu untuk penjualan kredit, retur, dan pelacakan piutang.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('master-data.pelanggan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

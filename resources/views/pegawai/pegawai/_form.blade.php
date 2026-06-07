<div class="grid gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(320px,0.9fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="label-text" for="nama">Nama Pegawai</label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $pegawai->nama ?? '') }}" class="input-field" placeholder="Nama lengkap pegawai" required>
            </div>
            <div>
                <label class="label-text" for="jabatan">Jabatan</label>
                <input id="jabatan" name="jabatan" type="text" value="{{ old('jabatan', $pegawai->jabatan ?? '') }}" class="input-field" placeholder="Kasir, Admin, Gudang">
            </div>
            <div>
                <label class="label-text" for="telepon">Telepon</label>
                <input id="telepon" name="telepon" type="text" value="{{ old('telepon', $pegawai->telepon ?? '') }}" class="input-field" placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="label-text" for="tanggal_masuk">Tanggal Masuk</label>
                <input id="tanggal_masuk" name="tanggal_masuk" type="date" value="{{ old('tanggal_masuk', isset($pegawai->tanggal_masuk) ? $pegawai->tanggal_masuk->format('Y-m-d') : '') }}" class="input-field">
            </div>
            <div>
                <label class="label-text" for="gaji_harian">Gaji Harian (Rp)</label>
                <input id="gaji_harian" name="gaji_harian" type="number" min="0" step="1"
                    value="{{ old('gaji_harian', $pegawai->gaji_harian ?? '') }}"
                    class="input-field" placeholder="0">
                @error('gaji_harian')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="label-text" for="status">Status</label>
                <select id="status" name="status" class="select-field" required>
                    <option value="aktif" @selected(old('status', $pegawai->status ?? 'aktif') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(old('status', $pegawai->status ?? '') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="textarea-field" placeholder="Alamat lengkap pegawai">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Aksi</h3>
        <p class="mt-2 text-sm text-slate-500">Data pegawai dapat dihubungkan ke akun user agar absensi dan aktivitas lebih mudah dilacak.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('pegawai.pegawai.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

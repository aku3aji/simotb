<div class="grid gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(320px,0.9fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="label-text" for="nama">Nama Vendor</label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $vendor->nama ?? '') }}" class="input-field" placeholder="Contoh: CV Bangun Makmur" required>
            </div>
            <div>
                <label class="label-text" for="kontak_person">Kontak Person</label>
                <input id="kontak_person" name="kontak_person" type="text" value="{{ old('kontak_person', $vendor->kontak_person ?? '') }}" class="input-field" placeholder="Nama PIC vendor">
            </div>
            <div>
                <label class="label-text" for="telepon">Telepon</label>
                <input id="telepon" name="telepon" type="text" value="{{ old('telepon', $vendor->telepon ?? '') }}" class="input-field" placeholder="08xxxxxxxxxx">
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $vendor->email ?? '') }}" class="input-field" placeholder="vendor@email.com">
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" class="textarea-field" placeholder="Alamat lengkap vendor">{{ old('alamat', $vendor->alamat ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Informasi Vendor</h3>
        <p class="mt-2 text-sm text-slate-500">Data vendor dipakai saat pembelian, sehingga kontak harus jelas agar pemesanan ulang lebih cepat.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('master-data.vendor.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

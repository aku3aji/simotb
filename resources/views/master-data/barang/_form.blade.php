<div class="grid gap-6 xl:grid-cols-[minmax(0,1.6fr)_minmax(320px,0.8fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="label-text" for="kode_barang">Kode Barang</label>
                <input id="kode_barang" name="kode_barang" type="text" value="{{ old('kode_barang', $barang->kode_barang ?? '') }}" class="input-field" placeholder="Contoh: MAT-SMN-001" required>
            </div>
            <div>
                <label class="label-text" for="nama">Nama Barang</label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $barang->nama ?? '') }}" class="input-field" placeholder="Nama barang" required>
            </div>
            <div>
                <label class="label-text" for="kategori_id">Kategori</label>
                <select id="kategori_id" name="kategori_id" class="select-field" required>
                    <option value="">Pilih kategori</option>
                    @foreach ($kategoriList as $item)
                        <option value="{{ $item->id }}" @selected(old('kategori_id', $barang->kategori_id ?? '') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="satuan_id">Satuan</label>
                <select id="satuan_id" name="satuan_id" class="select-field" required>
                    <option value="">Pilih satuan</option>
                    @foreach ($satuanList as $item)
                        <option value="{{ $item->id }}" @selected(old('satuan_id', $barang->satuan_id ?? '') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="merek_id">Merek</label>
                <select id="merek_id" name="merek_id" class="select-field">
                    <option value="">Tanpa merek</option>
                    @foreach ($merekList as $item)
                        <option value="{{ $item->id }}" @selected(old('merek_id', $barang->merek_id ?? '') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="harga_beli">Harga Beli</label>
                <input id="harga_beli" name="harga_beli" type="number" min="0" step="0.01" value="{{ old('harga_beli', $barang->harga_beli ?? 0) }}" class="input-field" required>
            </div>
            <div>
                <label class="label-text" for="harga_jual">Harga Jual</label>
                <input id="harga_jual" name="harga_jual" type="number" min="0" step="0.01" value="{{ old('harga_jual', $barang->harga_jual ?? 0) }}" class="input-field" required>
            </div>
            <div>
                <label class="label-text" for="stok">Stok Saat Ini</label>
                @if(auth()->user()->isOwner())
                    <input id="stok" name="stok" type="number" min="0" step="1" value="{{ old('stok', $barang->stok ?? 0) }}" class="input-field">
                    <p class="hint-text">Perubahan stok manual akan dicatat ke histori mutasi.</p>
                @else
                    <input id="stok" type="number" value="{{ old('stok', $barang->stok ?? 0) }}" class="input-field bg-slate-100" readonly>
                    <p class="hint-text text-amber-600">Stok hanya dapat diubah oleh Owner melalui Stock Opname.</p>
                @endif
            </div>
            <div>
                <label class="label-text" for="stok_minimum">Stok Minimum</label>
                <input id="stok_minimum" name="stok_minimum" type="number" min="0" step="1" value="{{ old('stok_minimum', $barang->stok_minimum ?? 0) }}" class="input-field" required>
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="textarea-field" placeholder="Deskripsi singkat barang">{{ old('deskripsi', $barang->deskripsi ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="surface p-6">
            <h3 class="text-lg font-bold text-slate-900">Status Barang</h3>
            <label class="mt-4 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-200" {{ old('is_active', $barang->is_active ?? true) ? 'checked' : '' }}>
                <span class="text-sm font-medium text-slate-700">Barang aktif dan dapat digunakan di transaksi</span>
            </label>
        </section>

        <section class="surface p-6">
            <h3 class="text-lg font-bold text-slate-900">Aksi</h3>
            <p class="mt-2 text-sm text-slate-500">Perubahan stok manual tetap akan dicatat ke histori mutasi stok sistem.</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                <a href="{{ route('master-data.barang.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </section>
    </aside>
</div>

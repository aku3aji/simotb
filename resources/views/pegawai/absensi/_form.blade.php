<div class="grid gap-6 xl:grid-cols-[minmax(0,1.3fr)_minmax(320px,0.9fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="label-text" for="pegawai_id">Pegawai</label>
                <select id="pegawai_id" name="pegawai_id" class="select-field" required>
                    <option value="">Pilih pegawai</option>
                    @foreach ($pegawaiList as $item)
                        <option value="{{ $item->id }}" @selected(old('pegawai_id', $absensi->pegawai_id ?? '') == $item->id)>{{ $item->nama }}{{ $item->jabatan ? ' - ' . $item->jabatan : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="tanggal">Tanggal</label>
                <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', isset($absensi->tanggal) ? $absensi->tanggal->format('Y-m-d') : now()->format('Y-m-d')) }}" class="input-field" required>
            </div>
            <div>
                <label class="label-text" for="status">Status Kehadiran</label>
                <select id="status" name="status" class="select-field" required>
                    @foreach (['hadir' => 'Hadir', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $absensi->status ?? 'hadir') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="jam_masuk">Jam Masuk</label>
                <input id="jam_masuk" name="jam_masuk" type="time" value="{{ old('jam_masuk', $absensi->jam_masuk ?? '') }}" class="input-field">
            </div>
            <div>
                <label class="label-text" for="jam_keluar">Jam Keluar</label>
                <input id="jam_keluar" name="jam_keluar" type="time" value="{{ old('jam_keluar', $absensi->jam_keluar ?? '') }}" class="input-field">
            </div>
            <div class="md:col-span-2">
                <label class="label-text" for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="textarea-field" placeholder="Catatan tambahan absensi">{{ old('keterangan', $absensi->keterangan ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <aside class="surface p-6">
        <h3 class="text-lg font-bold text-slate-900">Catatan Absensi</h3>
        <p class="mt-2 text-sm text-slate-500">Absensi digunakan untuk laporan kedisiplinan dan rekap kehadiran pegawai.</p>

        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </aside>
</div>

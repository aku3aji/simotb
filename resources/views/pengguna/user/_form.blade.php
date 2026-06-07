<div class="grid gap-6 xl:grid-cols-[minmax(0,1.4fr)_minmax(320px,0.9fr)]">
    <section class="surface p-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div>
                <label class="label-text" for="name">Nama User</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name ?? '') }}" class="input-field" placeholder="Nama akun" required>
            </div>
            <div>
                <label class="label-text" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" class="input-field" placeholder="email user" required>
            </div>
            <div>
                <label class="label-text" for="role">Role</label>
                <select id="role" name="role" class="select-field" required>
                    <option value="owner" @selected(old('role', $user->role ?? 'admin') === 'owner')>Owner</option>
                    <option value="admin" @selected(old('role', $user->role ?? 'admin') === 'admin')>Admin</option>
                </select>
            </div>
            <div>
                <label class="label-text" for="pegawai_id">Pegawai Terkait</label>
                <select id="pegawai_id" name="pegawai_id" class="select-field">
                    <option value="">Tidak dihubungkan</option>
                    @foreach ($pegawaiList as $item)
                        <option value="{{ $item->id }}" @selected(old('pegawai_id', $user->pegawai_id ?? '') == $item->id)>{{ $item->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label-text" for="password">Password {{ isset($user) ? 'Baru' : '' }}</label>
                <input id="password" name="password" type="password" class="input-field" placeholder="{{ isset($user) ? 'Kosongkan jika tidak diubah' : 'Minimal 8 karakter' }}" {{ isset($user) ? '' : 'required' }}>
            </div>
            <div>
                <label class="label-text" for="password_confirmation">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input-field" placeholder="Ulangi password" {{ isset($user) ? '' : 'required' }}>
            </div>
        </div>
    </section>

    <aside class="space-y-6">
        <section class="surface p-6">
            <h3 class="text-lg font-bold text-slate-900">Status Akun</h3>
            <label class="mt-4 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-4">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-200" {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}>
                <span class="text-sm font-medium text-slate-700">Akun aktif dan dapat login</span>
            </label>
        </section>

        <section class="surface p-6">
            <h3 class="text-lg font-bold text-slate-900">Aksi</h3>
            <p class="mt-2 text-sm text-slate-500">Role owner dipakai untuk akses penuh, sedangkan admin fokus pada operasional harian.</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
                <a href="{{ route('pengguna.user.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </section>
    </aside>
</div>

@extends('layouts.app')

@section('title', 'Profil Akun')

@section('content')
    <x-ui.page-header title="Profil Akun" description="Kelola informasi akun dan keamanan login Anda." />

    @include('partials.form-errors')

    <div class="max-w-lg">
        <section class="surface p-6">
            <form method="POST" action="{{ route('profil.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div>
                    <label class="label-text" for="name">Nama</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="input-field" required>
                </div>

                <div>
                    <label class="label-text" for="email">Email</label>
                    <input id="email" type="email" value="{{ $user->email }}" class="input-field bg-slate-50" readonly>
                    <p class="hint-text mt-1">Email tidak dapat diubah.</p>
                </div>

                <div>
                    <label class="label-text" for="role">Role</label>
                    <input id="role" type="text" value="{{ ucfirst($user->role) }}" class="input-field bg-slate-50" readonly>
                </div>

                <hr class="border-slate-200">

                <div>
                    <label class="label-text" for="password">
                        Password Baru
                        <span class="text-slate-400 font-normal">(kosongkan jika tidak diganti)</span>
                    </label>
                    <input id="password" name="password" type="password" class="input-field" autocomplete="new-password">
                </div>

                <div>
                    <label class="label-text" for="password_confirmation">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="input-field" autocomplete="new-password">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </div>
@endsection

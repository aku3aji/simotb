@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="surface mx-auto max-w-[560px] p-8 sm:p-10">
        <div class="mb-6 flex justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <x-ui.icon name="lock-open" class="h-7 w-7" />
            </div>
        </div>

        <h2 class="text-center text-3xl font-extrabold text-slate-900">Buat Password Baru</h2>
        <p class="mt-3 text-center text-base text-slate-600">Masukkan email Anda dan buat password baru.</p>

        @include('partials.form-errors')

        <form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label class="label-text" for="email">Email</label>
                <div class="relative">
                    <x-ui.icon name="mail" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input id="email" name="email" type="email" value="{{ old('email', $email ?? '') }}"
                        class="input-field pl-12" placeholder="email@toko.com" required autofocus>
                </div>
            </div>

            <div>
                <label class="label-text" for="password">Password Baru</label>
                <div class="relative">
                    <x-ui.icon name="clipboard-list" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input id="password" name="password" type="password"
                        class="input-field pl-12 pr-12" placeholder="Minimal 8 karakter" required>
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-700" data-password-toggle="password">
                        <x-ui.icon name="eye" class="h-5 w-5 block" data-icon-eye />
                        <x-ui.icon name="eye-off" class="h-5 w-5 hidden" data-icon-eye-off />
                    </button>
                </div>
            </div>

            <div>
                <label class="label-text" for="password_confirmation">Konfirmasi Password Baru</label>
                <div class="relative">
                    <x-ui.icon name="clipboard-list" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input id="password_confirmation" name="password_confirmation" type="password"
                        class="input-field pl-12" placeholder="Ulangi password baru" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success w-full justify-center py-3 text-base">
                <x-ui.icon name="check-circle" class="h-5 w-5" />
                Simpan Password Baru
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Kembali ke Login</a>
        </p>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.querySelector('[data-password-toggle]');
            if (!toggleBtn) return;
            const input = document.getElementById(toggleBtn.getAttribute('data-password-toggle'));
            const eyeIcon    = toggleBtn.querySelector('[data-icon-eye]');
            const eyeOffIcon = toggleBtn.querySelector('[data-icon-eye-off]');

            toggleBtn.addEventListener('click', function () {
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                eyeIcon?.classList.toggle('hidden', isPassword);
                eyeIcon?.classList.toggle('block', !isPassword);
                eyeOffIcon?.classList.toggle('hidden', !isPassword);
                eyeOffIcon?.classList.toggle('block', isPassword);
            });
        });
    </script>
@endpush

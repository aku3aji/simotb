@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="surface mx-auto max-w-[560px] p-8 sm:p-10">
        <h2 class="text-3xl font-extrabold text-slate-900 text-center">Selamat Datang Kembali</h2>
        <p class="mt-3 text-base text-slate-600 text-center">Silakan masukkan kredensial Anda untuk melanjutkan operasional toko.</p>

        @include('partials.form-errors')

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-6">
            @csrf

            <div>
                <label class="label-text" for="email">Username atau Email</label>
                <div class="relative">
                    <x-ui.icon name="user-circle" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        class="input-field pl-12"
                        placeholder="admin@sumberalamjaya.com"
                        required
                        autofocus
                    >
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label class="label-text !mb-0" for="password">Password</label>
                    <span class="text-sm text-brand-700">Hubungi owner jika lupa sandi.</span>
                </div>
                <div class="relative">
                    <x-ui.icon name="clipboard-list" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="input-field pl-12 pr-12"
                        placeholder="Masukkan password"
                        required
                    >
                    <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 transition hover:text-slate-700" data-password-toggle>
                        <x-ui.icon name="eye" class="h-5 w-5 block" data-icon-eye />
                        <x-ui.icon name="eye-off" class="h-5 w-5 hidden" data-icon-eye-off />
                    </button>
                </div>
            </div>

            <label class="flex items-center gap-3 rounded-md border border-slate-200 bg-slate-50 px-4 py-3">
                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-700 focus:ring-brand-200" {{ old('remember') ? 'checked' : '' }}>
                <span class="text-sm text-slate-700">Ingat saya di perangkat ini</span>
            </label>

            <button type="submit" class="btn btn-primary w-full justify-center py-3 text-base">
                Masuk ke Sistem
                <x-ui.icon name="chevron-right" class="h-5 w-5" />
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('[data-password-toggle]');

            toggleButton?.addEventListener('click', function () {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                toggleButton.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
                
                const eyeIcon = toggleButton.querySelector('[data-icon-eye]');
                const eyeOffIcon = toggleButton.querySelector('[data-icon-eye-off]');
                
                if (isPassword) {
                    eyeIcon?.classList.add('hidden');
                    eyeIcon?.classList.remove('block');
                    eyeOffIcon?.classList.remove('hidden');
                    eyeOffIcon?.classList.add('block');
                } else {
                    eyeOffIcon?.classList.add('hidden');
                    eyeOffIcon?.classList.remove('block');
                    eyeIcon?.classList.remove('hidden');
                    eyeIcon?.classList.add('block');
                }
            });
        });
    </script>
@endpush

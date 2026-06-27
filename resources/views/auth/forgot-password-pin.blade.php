@extends('layouts.auth')

@section('title', 'Lupa Password')

@section('content')
    <div class="surface mx-auto max-w-[560px] p-8 sm:p-10">
        <div class="mb-6 flex justify-center">
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-brand-700">
                <x-ui.icon name="mail" class="h-7 w-7" />
            </div>
        </div>

        <h2 class="text-center text-3xl font-extrabold text-slate-900">Lupa Password?</h2>
        <p class="mt-3 text-center text-base text-slate-600">Masukkan email akun Anda. Kami akan mengirimkan link untuk mereset password.</p>

        @if (session('success'))
            <div class="mt-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-4 text-sm text-emerald-800">
                <p class="font-semibold">Email berhasil dikirim!</p>
                <p class="mt-1">{{ session('success') }}</p>
            </div>
        @endif

        @include('partials.form-errors')

        <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
            @csrf

            <div>
                <label class="label-text" for="email">Email</label>
                <div class="relative">
                    <x-ui.icon name="mail" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                    <input id="email" name="email" type="email" value="{{ old('email') }}"
                        class="input-field pl-12" placeholder="email@toko.com" required autofocus>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-full justify-center py-3 text-base">
                Kirim Link Reset Password
                <x-ui.icon name="send" class="h-5 w-5" />
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Ingat password?
            <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Kembali ke Login</a>
        </p>
    </div>
@endsection

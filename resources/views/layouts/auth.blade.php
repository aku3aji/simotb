<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Login') - Sumber Alam Jaya</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-app-glow">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-y-0 left-0 w-[40vw] min-w-[260px] bg-brand-100/70 blur-3xl"></div>
        <div class="absolute bottom-0 right-0 h-72 w-72 rounded-full bg-accent-100/50 blur-3xl"></div>

        <main class="relative flex min-h-screen items-center justify-center px-4 py-10 sm:px-6">
            <div class="w-full max-w-xl">
                <div class="mb-8 text-center">
                    <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-lg border border-brand-200 bg-white shadow-panel">
                        <x-ui.icon name="building" class="h-9 w-9 text-brand-700" />
                    </div>
                    <h1 class="text-4xl font-extrabold tracking-tight text-brand-700 sm:text-5xl">Sumber Alam Jaya</h1>
                    <p class="mt-3 text-lg text-slate-700">Sistem Manajemen Operasional</p>
                </div>

                @include('partials.flash')

                @yield('content')

                <p class="mt-10 text-center text-sm text-slate-600">&copy; {{ now()->year }} Sumber Alam Jaya. Hak Cipta Dilindungi.</p>
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>

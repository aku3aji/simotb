@php
    $currentUser = auth()->user();
    $avatarText = collect(explode(' ', $currentUser->name ?? 'P U'))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');
@endphp

<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur">
    <div class="flex items-center gap-3 px-4 py-4 sm:px-6 lg:px-10">
        <button type="button" class="btn btn-secondary px-2.5 py-2 lg:hidden" data-sidebar-open>
            <x-ui.icon name="menu" class="h-4 w-4" />
        </button>

        <form method="GET" action="{{ url()->current() }}" class="hidden min-w-0 flex-1 md:block">
            <div class="relative max-w-2xl">
                <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" />
                <input type="text" name="q" value="{{ request('q') }}" class="input-field pl-12 pr-24" placeholder="Cari barang, transaksi, atau menu..." />
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-semibold text-slate-500">
                    Cari
                </button>
            </div>
        </form>

        <div class="ml-auto flex items-center gap-2 sm:gap-3">
            <button type="button" class="btn btn-secondary hidden px-2.5 py-2 sm:inline-flex">
                <x-ui.icon name="bell" class="h-4 w-4" />
            </button>
            <button type="button" class="btn btn-secondary hidden px-2.5 py-2 sm:inline-flex">
                <x-ui.icon name="settings" class="h-4 w-4" />
            </button>
            <button type="button" class="btn btn-secondary hidden px-2.5 py-2 sm:inline-flex">
                <x-ui.icon name="circle-help" class="h-4 w-4" />
            </button>

            <div class="hidden h-10 w-px bg-slate-200 sm:block"></div>

            <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2">
                <div class="hidden text-right sm:block">
                    <p class="text-sm font-semibold text-slate-900">{{ $currentUser->name }}</p>
                    <p class="text-xs text-slate-500">{{ ucfirst($currentUser->role) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 font-bold text-white">
                    {{ $avatarText ?: 'PU' }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-slate-400 transition hover:text-slate-700" title="Logout">
                        <x-ui.icon name="log-out" class="h-4 w-4" />
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

@php
    $user = auth()->user();
    $initials = collect(explode(' ', $user->name ?? 'S A'))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');

    $menu = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'active' => request()->routeIs('dashboard')],
        ['label' => 'Manajemen Barang', 'route' => 'master-data.barang.index', 'icon' => 'package', 'active' => request()->routeIs('master-data.*')],
        ['label' => 'Stok & Opname', 'route' => 'inventory.stok-opname.index', 'icon' => 'boxes', 'active' => request()->routeIs('inventory.*')],
        ['label' => 'Penjualan', 'route' => 'transaksi.penjualan.index', 'icon' => 'shopping-cart', 'active' => request()->routeIs('transaksi.penjualan.*')],
        ['label' => 'Pembelian', 'route' => 'transaksi.pembelian.index', 'icon' => 'receipt', 'active' => request()->routeIs('transaksi.pembelian.*')],
        ['label' => 'Piutang', 'route' => 'transaksi.pembayaran-piutang.index', 'icon' => 'wallet', 'active' => request()->routeIs('transaksi.pembayaran-piutang.*')],
        ['label' => 'Retur Penjualan', 'route' => 'transaksi.retur-penjualan.index', 'icon' => 'rotate-ccw', 'active' => request()->routeIs('transaksi.retur-penjualan.*')],
        ['label' => 'Pegawai & Absensi', 'route' => 'pegawai.pegawai.index', 'icon' => 'users', 'active' => request()->routeIs('pegawai.*')],
        ['label' => 'Laporan', 'route' => 'laporan.stok', 'icon' => 'file-bar-chart', 'active' => request()->routeIs('laporan.*')],
    ];

    if ($user?->isOwner()) {
        $menu[] = ['label' => 'Manajemen User', 'route' => 'pengguna.user.index', 'icon' => 'clipboard-list', 'active' => request()->routeIs('pengguna.*')];
    }
@endphp

<div class="fixed inset-0 z-30 hidden bg-slate-950/20 lg:hidden" data-sidebar-overlay></div>

<aside class="fixed inset-y-0 left-0 z-40 flex w-[280px] -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:translate-x-0" data-sidebar>
    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-5">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-brand-700 text-white shadow-soft">
                <span class="text-xl font-extrabold">{{ $initials ?: 'SA' }}</span>
            </div>
            <div>
                <p class="text-2xl font-extrabold leading-tight text-brand-700">Sumber Alam Jaya</p>
                <p class="mt-1 text-sm text-slate-500">{{ ucfirst($user->role ?? 'admin') }} Operasional</p>
            </div>
        </div>

        <button type="button" class="btn btn-secondary px-2.5 py-2 lg:hidden" data-sidebar-close>
            <x-ui.icon name="x" class="h-4 w-4" />
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-5">
        <p class="px-3 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Navigasi</p>
        <nav class="mt-3 space-y-1.5">
            @foreach ($menu as $item)
                <a href="{{ route($item['route']) }}" class="sidebar-link {{ $item['active'] ? 'sidebar-link-active' : '' }}">
                    <x-ui.icon :name="$item['icon']" class="h-5 w-5" />
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

</aside>

@php
    $user = auth()->user();
    $initials = collect(explode(' ', $user->name ?? 'S A'))
        ->filter()
        ->take(2)
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->implode('');

    $masterDataActive = request()->routeIs('master-data.barang.*', 'master-data.kategori.*', 'master-data.satuan.*', 'master-data.merek.*');

    $menu = [
        ['type' => 'section', 'label' => 'Umum'],
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'layout-dashboard', 'active' => request()->routeIs('dashboard')],

        ['type' => 'section', 'label' => 'Master Data'],
        [
            'label' => 'Manajemen Barang',
            'icon' => 'package',
            'active' => $masterDataActive,
            'open' => $masterDataActive,
            'children' => [
                ['label' => 'Daftar Barang', 'route' => 'master-data.barang.index', 'active' => request()->routeIs('master-data.barang.*')],
                ['label' => 'Kategori', 'route' => 'master-data.kategori.index', 'active' => request()->routeIs('master-data.kategori.*')],
                ['label' => 'Satuan', 'route' => 'master-data.satuan.index', 'active' => request()->routeIs('master-data.satuan.*')],
                ['label' => 'Merek', 'route' => 'master-data.merek.index', 'active' => request()->routeIs('master-data.merek.*')],
            ],
        ],
        ['label' => 'Pelanggan', 'route' => 'master-data.pelanggan.index', 'icon' => 'user', 'active' => request()->routeIs('master-data.pelanggan.*')],
        ['label' => 'Vendor', 'route' => 'master-data.vendor.index', 'icon' => 'truck', 'active' => request()->routeIs('master-data.vendor.*')],

        ['type' => 'section', 'label' => 'Transaksi'],
        ['label' => 'Penjualan', 'route' => 'transaksi.penjualan.index', 'icon' => 'shopping-cart', 'active' => request()->routeIs('transaksi.penjualan.*')],
        ['label' => 'Pembelian', 'route' => 'transaksi.pembelian.index', 'icon' => 'receipt', 'active' => request()->routeIs('transaksi.pembelian.*')],
        ['label' => 'Piutang', 'route' => 'transaksi.pembayaran-piutang.index', 'icon' => 'wallet', 'active' => request()->routeIs('transaksi.pembayaran-piutang.*')],
        ['label' => 'Retur Penjualan', 'route' => 'transaksi.retur-penjualan.index', 'icon' => 'rotate-ccw', 'active' => request()->routeIs('transaksi.retur-penjualan.*')],

        ['type' => 'section', 'label' => 'Operasional'],
        ['label' => 'Stok & Opname', 'route' => 'inventory.stok-opname.index', 'icon' => 'boxes', 'active' => request()->routeIs('inventory.*')],
    ];

    if ($user?->isOwner()) {
        $menu[] = ['label' => 'Pegawai & Absensi', 'route' => 'pegawai.pegawai.index', 'icon' => 'users', 'active' => request()->routeIs('pegawai.*')];
        $menu[] = ['type' => 'section', 'label' => 'Laporan'];
        $menu[] = ['label' => 'Laporan', 'route' => 'laporan.stok', 'icon' => 'file-bar-chart', 'active' => request()->routeIs('laporan.*')];
        $menu[] = ['type' => 'section', 'label' => 'Pengguna'];
        $menu[] = ['label' => 'Manajemen User', 'route' => 'pengguna.user.index', 'icon' => 'clipboard-list', 'active' => request()->routeIs('pengguna.*')];
    }
@endphp

<div class="fixed inset-0 z-30 hidden bg-slate-950/20 lg:hidden" data-sidebar-overlay></div>

<aside class="fixed inset-y-0 left-0 z-40 flex w-[280px] -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0" data-sidebar>
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
        <nav class="mt-3 space-y-1.5">
            @foreach ($menu as $item)
                @if (($item['type'] ?? '') === 'section')
                    <p class="px-3 pb-0.5 pt-4 text-xs font-semibold uppercase tracking-[0.16em] text-slate-400 first:pt-0">{{ $item['label'] }}</p>
                @elseif (isset($item['children']))
                    <div data-submenu-parent>
                        <button type="button"
                            class="sidebar-link w-full {{ $item['active'] ? 'sidebar-link-active' : '' }}"
                            data-submenu-toggle
                            aria-expanded="{{ $item['open'] ? 'true' : 'false' }}">
                            <x-ui.icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                            <span class="flex-1 text-left">{{ $item['label'] }}</span>
                            <x-ui.icon name="chevron-down" class="h-4 w-4 shrink-0 transition-transform duration-200 {{ $item['open'] ? 'rotate-180' : '' }}" data-submenu-chevron />
                        </button>
                        <div class="{{ $item['open'] ? '' : 'hidden' }} mt-1 space-y-0.5 overflow-hidden border-l-2 border-slate-100 ml-3 pl-3" data-submenu>
                            @foreach ($item['children'] as $child)
                                <a href="{{ route($child['route']) }}"
                                    class="sidebar-link py-2.5 text-sm {{ $child['active'] ? 'sidebar-link-active' : '' }}">
                                    <span>{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ route($item['route']) }}" class="sidebar-link {{ $item['active'] ? 'sidebar-link-active' : '' }}">
                        <x-ui.icon :name="$item['icon']" class="h-5 w-5" />
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach
        </nav>
    </div>

</aside>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-submenu-toggle]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const submenu = this.parentElement.querySelector('[data-submenu]');
                    const chevron = this.querySelector('[data-submenu-chevron]');
                    const isOpen = !submenu.classList.contains('hidden');

                    submenu.classList.toggle('hidden', isOpen);
                    chevron.classList.toggle('rotate-180', !isOpen);
                    this.setAttribute('aria-expanded', String(!isOpen));
                });
            });
        </script>
    @endpush
@endonce

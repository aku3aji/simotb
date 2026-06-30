<div class="mb-6 flex gap-2 overflow-x-auto">
    <a href="{{ route('laporan.stok') }}" class="btn {{ request()->routeIs('laporan.stok') ? 'btn-primary' : 'btn-secondary' }}">Stok</a>
    <a href="{{ route('laporan.stok-masuk') }}" class="btn {{ request()->routeIs('laporan.stok-masuk') ? 'btn-primary' : 'btn-secondary' }}">Stok Masuk</a>
    <a href="{{ route('laporan.stok-keluar') }}" class="btn {{ request()->routeIs('laporan.stok-keluar') ? 'btn-primary' : 'btn-secondary' }}">Stok Keluar</a>
    <a href="{{ route('laporan.piutang') }}" class="btn {{ request()->routeIs('laporan.piutang') ? 'btn-primary' : 'btn-secondary' }}">Piutang</a>
    <a href="{{ route('laporan.mutasi-stok') }}" class="btn {{ request()->routeIs('laporan.mutasi-stok') ? 'btn-primary' : 'btn-secondary' }}">Mutasi Stok</a>
    <a href="{{ route('laporan.stok-opname') }}" class="btn {{ request()->routeIs('laporan.stok-opname') ? 'btn-primary' : 'btn-secondary' }}">Stok Opname</a>
    <a href="{{ route('laporan.absensi') }}" class="btn {{ request()->routeIs('laporan.absensi') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
</div>

@extends('layouts.app')

@section('title', 'User')

@section('content')
    <x-ui.page-header title="Manajemen User" description="Kelola akun owner dan admin yang dapat mengakses sistem.">
        <div class="hidden items-center gap-3" data-bulk-bar>
            <span class="text-sm font-semibold text-slate-700"><span data-bulk-count>0</span> dipilih</span>
            <button form="bulk-form" type="submit" class="btn btn-danger">
                <x-ui.icon name="trash-2" class="h-4 w-4" />
                <span>Hapus Terpilih</span>
            </button>
        </div>
        <a href="{{ route('pengguna.user.create') }}" class="btn btn-primary">
            <x-ui.icon name="plus" class="h-4 w-4" />
            <span>Tambah User</span>
        </a>
    </x-ui.page-header>

    <form id="bulk-form" method="POST" action="{{ route('pengguna.user.bulk-destroy') }}"
          data-confirm="Hapus semua user yang dipilih? Akun sendiri tidak akan ikut terhapus.">
        @csrf
        @method('DELETE')
    </form>

    <section class="surface overflow-hidden">
        <form method="GET" class="border-b border-slate-200 px-5 py-4">
            <div class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_180px_180px_auto]">
                <div class="relative">
                    <x-ui.icon name="search" class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="text" name="q" value="{{ $q }}" class="input-field pl-11" placeholder="Cari nama user, email, atau pegawai">
                </div>
                <select name="role" class="select-field">
                    <option value="">Semua role</option>
                    <option value="owner" @selected($role === 'owner')>Owner</option>
                    <option value="admin" @selected($role === 'admin')>Admin</option>
                </select>
                <select name="status" class="select-field">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected($status === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                </select>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </div>
        </form>

        @if ($users->isEmpty())
            <x-ui.empty-state title="User belum tersedia" description="Buat akun pertama untuk mulai mengakses sistem." icon="users" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-10 !px-3"><input type="checkbox" data-select-all form="bulk-form" class="h-4 w-4 cursor-pointer rounded"></th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Pegawai</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th class="!text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $item)
                            <tr>
                                <td class="!px-3">
                                    @if ($item->id !== auth()->id())
                                        <input type="checkbox" name="ids[]" value="{{ $item->id }}" data-row-cb form="bulk-form" class="h-4 w-4 cursor-pointer rounded">
                                    @endif
                                </td>
                                <td>
                                    <div class="font-semibold text-slate-900">{{ $item->name }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $item->email }}</div>
                                </td>
                                <td><span class="badge badge-primary">{{ ucfirst($item->role) }}</span></td>
                                <td>{{ $item->pegawai->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $item->is_active ? 'badge-success' : 'badge-muted' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                </td>
                                <td>{{ optional($item->created_at)->translatedFormat('d M Y') }}</td>
                                <td>
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ route('pengguna.user.edit', $item) }}" class="btn btn-secondary px-3 py-2">
                                            <x-ui.icon name="pencil" class="h-4 w-4" />
                                        </a>
                                        @if ($item->id !== auth()->id())
                                            <form method="POST" action="{{ route('pengguna.user.destroy', $item) }}" data-confirm="Hapus user '{{ $item->name }}'?">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger px-3 py-2">
                                                    <x-ui.icon name="trash" class="h-4 w-4" />
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $users->links() }}
        @endif
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Catat Absensi Massal')

@section('content')
    <x-ui.page-header title="Catat Absensi Massal" description="Input kehadiran seluruh pegawai aktif dalam satu form sekaligus.">
        <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-secondary">Kembali</a>
    </x-ui.page-header>

    <div class="mb-6 flex gap-2 overflow-x-auto">
        <a href="{{ route('pegawai.pegawai.index') }}" class="btn {{ request()->routeIs('pegawai.pegawai.*') ? 'btn-primary' : 'btn-secondary' }}">Data Pegawai</a>
        <a href="{{ route('pegawai.absensi.index') }}" class="btn {{ request()->routeIs('pegawai.absensi.*') ? 'btn-primary' : 'btn-secondary' }}">Absensi</a>
    </div>

    @include('partials.form-errors')

    @if ($pegawaiList->isEmpty())
        <x-ui.empty-state title="Belum ada pegawai aktif" description="Tambahkan pegawai terlebih dahulu sebelum mencatat absensi." icon="users" />
    @else
        <form method="POST" action="{{ route('pegawai.absensi.store-massal') }}">
            @csrf

            <div class="surface mb-6 overflow-hidden">
                <div class="flex flex-wrap items-center gap-4 border-b border-slate-200 px-5 py-4">
                    <div>
                        <label class="label-text mb-1" for="tanggal">Tanggal Absensi</label>
                        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $tanggal) }}"
                               class="input-field w-auto" required>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">
                        Pegawai yang sudah memiliki absensi pada tanggal ini akan dilewati secara otomatis.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="w-[220px]">Pegawai</th>
                                <th class="w-[140px]">Status</th>
                                <th class="w-[120px]">Jam Masuk</th>
                                <th class="w-[120px]">Jam Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pegawaiList as $item)
                                <tr data-absensi-row="{{ $item->id }}">
                                    <td>
                                        <div class="font-semibold text-slate-900">{{ $item->nama }}</div>
                                        @if ($item->jabatan)
                                            <div class="mt-0.5 text-xs text-slate-500">{{ $item->jabatan }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="absensi[{{ $item->id }}][status]"
                                                class="select-field py-2 text-sm"
                                                data-status-select="{{ $item->id }}">
                                            <option value="hadir">Hadir</option>
                                            <option value="izin">Izin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="alpha">Alpha</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input name="absensi[{{ $item->id }}][jam_masuk]"
                                               type="time"
                                               value="07:00"
                                               class="input-field py-2 text-sm"
                                               data-jam="{{ $item->id }}">
                                    </td>
                                    <td>
                                        <input name="absensi[{{ $item->id }}][jam_keluar]"
                                               type="time"
                                               value="16:00"
                                               class="input-field py-2 text-sm"
                                               data-jam="{{ $item->id }}">
                                    </td>
                                    <td>
                                        <input name="absensi[{{ $item->id }}][keterangan]"
                                               type="text"
                                               class="input-field py-2 text-sm"
                                               placeholder="Catatan (opsional)">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-3 border-t border-slate-200 px-5 py-4">
                    <a href="{{ route('pegawai.absensi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <x-ui.icon name="save" class="h-4 w-4" />
                        <span>Simpan Semua ({{ $pegawaiList->count() }} Pegawai)</span>
                    </button>
                </div>
            </div>
        </form>
    @endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-status-select]').forEach(function (select) {
            const id = select.dataset.statusSelect;
            const jamInputs = document.querySelectorAll('[data-jam="' + id + '"]');

            const toggle = () => {
                const isHadir = select.value === 'hadir';
                jamInputs.forEach(input => {
                    input.disabled = !isHadir;
                    input.classList.toggle('opacity-40', !isHadir);
                });
            };

            select.addEventListener('change', toggle);
            toggle();
        });
    });
</script>
@endpush

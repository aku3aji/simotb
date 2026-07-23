@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <x-ui.page-header title="Pengaturan" description="Kelola kebijakan operasional toko yang hanya dapat diubah oleh owner." />

    @include('partials.form-errors')

    <div class="max-w-lg">
        <section class="surface p-6">
            <form method="POST" action="{{ route('pengaturan.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <h2 class="text-lg font-bold text-slate-900">Batas Jatuh Tempo Piutang</h2>
                    <p class="mt-1 text-sm text-slate-500">
                        Menentukan tenggat pembayaran maksimal untuk penjualan kredit (piutang).
                    </p>
                </div>

                <div>
                    <label class="label-text" for="maks_hari_jatuh_tempo">Batas Maksimal Jatuh Tempo (hari)</label>
                    <input id="maks_hari_jatuh_tempo" name="maks_hari_jatuh_tempo" type="number" min="1" max="365" step="1"
                        value="{{ old('maks_hari_jatuh_tempo', $maksHariJatuhTempo) }}"
                        class="input-field" required>
                    <p class="hint-text mt-1">
                        Tanggal jatuh tempo pada penjualan kredit tidak boleh melebihi jumlah hari ini dihitung sejak
                        tanggal transaksi. Contoh: <strong>30</strong> berarti pelanggan wajib melunasi paling lambat
                        30 hari setelah transaksi.
                    </p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </section>
    </div>
@endsection

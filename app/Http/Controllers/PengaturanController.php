<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengaturanController extends Controller
{
    public function edit(): View
    {
        return view('pengaturan.edit', [
            'maksHariJatuhTempo' => Pengaturan::maksHariJatuhTempo(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'maks_hari_jatuh_tempo' => ['required', 'integer', 'min:1', 'max:365'],
        ], [
            'maks_hari_jatuh_tempo.required' => 'Batas maksimal jatuh tempo wajib diisi.',
            'maks_hari_jatuh_tempo.integer'  => 'Batas maksimal jatuh tempo harus berupa angka bulat.',
            'maks_hari_jatuh_tempo.min'      => 'Batas maksimal jatuh tempo minimal :min hari.',
            'maks_hari_jatuh_tempo.max'      => 'Batas maksimal jatuh tempo maksimal :max hari.',
        ]);

        Pengaturan::set(Pengaturan::KEY_MAKS_HARI_JATUH_TEMPO, $validated['maks_hari_jatuh_tempo']);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }
}

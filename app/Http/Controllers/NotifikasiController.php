<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class NotifikasiController extends Controller
{
    public function data(): JsonResponse
    {
        $unread = Notifikasi::belumDibaca()->count();

        $items = Notifikasi::query()
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($n) => [
                'id'      => $n->id,
                'judul'   => $n->judul,
                'pesan'   => $n->pesan,
                'ikon'    => $n->ikon,
                'warna'   => $n->warna,
                'tautan'  => $n->tautan,
                'dibaca'  => $n->sudahDibaca(),
                'waktu'   => Carbon::parse($n->created_at)->diffForHumans(),
            ]);

        return response()->json(compact('unread', 'items'));
    }

    public function bacaSemua(): JsonResponse
    {
        Notifikasi::belumDibaca()->update(['dibaca_pada' => now()]);

        return response()->json(['ok' => true]);
    }
}
